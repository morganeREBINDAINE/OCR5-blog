<?php

namespace OCR5\Services;

use OCR5\Entities\Comment;
use OCR5\Handler\PostHandler;

class BackManager extends Manager
{
    /**
     * Create pagination system for the entity specified
     *
     * @param $entity
     * @param $limit
     * @param $condition
     *
     * @return array|null
     */
    public function getPagination($entity, $limit, $condition = null)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('error', 'Erreur : l\'entité injectée n\'existe pas.');

            return null;
        }

        $repository = $this->getHandler($entity);

        $pagination['nb'] = $repository->countValids();
        $pagination['pages']['max'] = round($pagination['nb'] / $limit);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $page = $page > $pagination['pages']['max'] ? $pagination['pages']['max'] : $page;

        $offset = $page > 0 ? ($page - 1) * $limit : 0;

        $pagination['pages']['actual'] = $page;
        $pagination['pages']['before'] = $page - 1;
        $pagination['pages']['after'] = $page + 1;
        $pagination[$entity . 's'] = $repository->getValids((int)$limit, $offset, $condition);

        return $pagination;
    }

    /**
     * Create a table for the entity specified (fields are written in the entity class)
     *
     * @param      $entity
     * @param bool $valid
     * @param null $id
     *
     * @return array|null
     */
    public function createTable($entity, $valid = false, $id = null)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('errorClass', 'Le formulaire ne peut être créé: vérifier que la classe existe et qu\'elle implémente bien EntityInterface.');
            return null;
        }

        $fqcn = $this->getEntityFQCN($entity);
        $handler = $this->getHandler($entity);

        if ($entity === 'post' && $id !== null) {
            $postHandler = new PostHandler();
            $entities = $postHandler->getByUser($id);
            $fields = $fqcn::getPrivateFields();
            $form['postsByUser'] = true;
        } else {
            $fields = $fqcn::getPublicFields();
            $entities = $valid === true ? $handler->getValids() : $handler->getRequests();
        }

        $form['traductedEntity'] = $fqcn::getRequestedTraduction();
        $form['entity'] = $entity;
        $form['type'] = $valid === true ? 'valids' : 'requests';

        foreach ($fields as $attribute => $label) {
            $form['labels'][] = $label;
            $i = 0;

            if ($entities) {
                foreach ($entities as $entity) {
                    $function                   = 'get' . ucfirst($attribute);

                    if ($entity instanceof Comment && $attribute === 'post') {
                        $form['row'][$i]['datas'][] = '<a href="/article-'.$entity->$function().'" class="btn btn-warning">Voir</a>';
                    } else {
                        $form['row'][$i]['datas'][] = $entity->$function();
                    }

                    $form['row'][$i]['id']      = $entity->getId();
                    $i++;
                }
            }
        }

        return $form;
    }
}
