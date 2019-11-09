<?php

namespace OCR5\Services;

class BackManager extends Manager
{
    public function getRequests($entity)
    {
        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 0', [], 'OCR5\Entities\\'. ucfirst($entity), true);
    }

    public function getValids($entity, $limit = null, $offset = null, $id = null)
    {
        $andWhere = $entity === 'user' ? ' AND role = "contributor"' : null;

        $limit = ((null !== $limit) && (null !== $limit)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;
//        var_dump('SELECT * FROM '.$entity.' WHERE status = 1 ' . $andWhere . ' ORDER BY id DESC ' . $limit);die;

        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 1 ' . $andWhere . ' ORDER BY id DESC ' . $limit, [], 'OCR5\Entities\\'. ucfirst($entity), true);
    }

    public function getValid($entity, $id)
    {
        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 1 AND id = :id', [
            ':id' => $id
        ], 'OCR5\Entities\\'. ucfirst($entity));
    }

    public function getPagination($entity, $limit)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('error', 'Erreur : l\'entité injectée n\'existe pas.');
            return null;
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $pagination[$entity] = $this->getValids($entity, (int)$limit, $offset);
        $pagination['nb'] = $this->queryDatabase('SELECT COUNT(*) as count FROM ' . $entity . ' WHERE status = 1')['count'];
        $pagination['pages']['max'] = $pagination['nb'] / $limit;
        $pagination['pages']['actual'] = $page;
        $pagination['pages']['before'] = $page - 1;
        $pagination['pages']['after'] = $page + 1;

        return $pagination;
    }

    public function createTable($entity, $valid = false, $id = null)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('errorClass', 'Le formulaire ne peut être créé: vérifier que la classe existe et qu\'elle implémente bien EntityInterface.');
            return null;
        }

        $fqcn = $this->getEntityFQCN($entity);
        if ($entity === 'post' && is_null($id) === false) {
            $entities = $this->getPostsByUser($id);
            $form['byUser'] = true;
        } else {
            $entities = $valid === true ? $this->getValids($entity) : $this->getRequests($entity);
        }

        $form['traductedEntity'] = $fqcn::getRequestedTraduction();
        $form['entity'] = $entity;
        $form['type'] = $valid === true ? 'valids' : 'requests';

        $fields = $fqcn::getPublicFields();

        foreach ($fields as $attribute => $label) {
            $form['labels'][] = $label;
            $i = 0;

            if ($entities) {
                foreach ($entities as $entity) {
                    $function                   = 'get' . ucfirst($attribute);
                    $form['row'][$i]['datas'][] = $entity->$function();
                    $form['row'][$i]['id']      = $entity->getId();
                    $i++;
                }
            }
        }

        return $form;
    }

    public function handleEntity($entity, $id, $status)
    {
        if (false === in_array($status, [0,1])) {
            return null;
        }
        return $this->queryDatabase('UPDATE '.$entity. ' SET status = '.$status.' WHERE id = :id', [
            ':id' => $id
        ]);
    }

    public function getPostsByUser($id) {
        return $this->queryDatabase('SELECT * FROM post WHERE status = 1 AND id = :id ORDER BY id DESC', [
            ':id' => $id
        ], 'OCR5\Entities\Post', true);

    }
}
