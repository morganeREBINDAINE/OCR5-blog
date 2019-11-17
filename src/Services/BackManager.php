<?php

namespace OCR5\Services;

use OCR5\Entities\Comment;

class BackManager extends Manager
{
    public function getRequests($entity)
    {
        return $this->select('*', $entity, 'status = 0', null, [], true);
    }

    public function getValids($entity, $limit = null, $offset = null, $condition = null)
    {
        $andWhere = $entity === 'user' ? ' AND role = "contributor" ' : null;
        $andWhere .= ($condition !== null) ? ' AND ' . $condition : null;

        $limit = ((null !== $limit) && (null !== $offset)) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 1 ' . $andWhere . ' ORDER BY id DESC ' . $limit, [], 'OCR5\Entities\\'. ucfirst($entity), true);
    }

    public function getValid($entity, $id)
    {
        $innerJoin = ($entity === 'post') ? ' INNER JOIN user u ON p.user_id = u.id' : null;
        $table = ' ' . substr($entity, 0, 1);

//        return $this->select('*', $entity,'status = 1 AND id = :id ', null, [
//            ':id' => $id
//        ]);
        return $this->queryDatabase('SELECT * FROM '.$entity.$table.$innerJoin.' WHERE '.$table.'.status = 1 AND '.$table.'.id = :id ', [
            ':id' => $id
        ], 'OCR5\Entities\\'. ucfirst($entity));
    }

    public function getPagination($entity, $limit, $condition = null)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('error', 'Erreur : l\'entité injectée n\'existe pas.');

            return null;
        }
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

//        ;

        $pagination[$entity . 's'] = $this->getRepository($entity)->getValids((int)$limit, $offset, $condition);
        $pagination['nb'] = $this->queryDatabase('SELECT COUNT(*) as count FROM ' . $entity . ' WHERE status = 1')['count'];
        $pagination['pages']['max'] = $pagination['nb'] / $limit;
        $pagination['pages']['actual'] = $page;
        $pagination['pages']['before'] = $page - 1;
        $pagination['pages']['after'] = $page + 1;

        return $pagination;
    }

    public function getPaginatedCommentsByPost($limit, $id)
    {
        return $this->getPagination('comment', $limit, 'post_id = '. (int)$id);
    }

    public function createTable($entity, $valid = false, $id = null)
    {
        if (false === $this->entityExists($entity)) {
            $this->addFlash('errorClass', 'Le formulaire ne peut être créé: vérifier que la classe existe et qu\'elle implémente bien EntityInterface.');
            return null;
        }

        $fqcn = $this->getEntityFQCN($entity);
        if ($entity === 'post' && $id !== null) {
            $entities = $this->getPostsByUser($id);
            $form['postsByUser'] = true;
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

    public function handleEntity($entity, $id, $status)
    {
        $entity = $this->getEntityFQCN($entity);
        if (false === in_array($status, [1,2,3])) {
            return null;
        }
        return $this->queryDatabase('UPDATE '.$entity. ' SET status = '.$status.' WHERE id = :id', [
            ':id' => $id
        ]);
    }

    public function getPostsByUser($id)
    {
        return $this->queryDatabase('SELECT * FROM post WHERE status = 1 AND user_id = :id ORDER BY id DESC', [
            ':id' => $id
        ], 'OCR5\Entities\Post', true);
    }

    public function getPost($id)
    {
        return $this->select('*', 'post', 'id = :id', null, [
            ':id' => $id
        ], 'post');
    }
}
