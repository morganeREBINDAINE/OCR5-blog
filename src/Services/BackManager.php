<?php

namespace OCR5\Services;

class BackManager extends Manager
{
    public function getRequests($entity)
    {
        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 0', [], 'OCR5\Entities\\'. ucfirst($entity), true);
    }

    public function getValids($entity, $limit = null, $offset = null)
    {
        $andWhere = $entity === 'user' ? ' AND role = "contributor"' : null;
        $limit = (null !== $limit) && (null !== $limit) ? ' LIMIT '.$limit.' OFFSET '.$offset : null;

        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 1' . $andWhere . $limit, [], 'OCR5\Entities\\'. ucfirst($entity), true);
    }

    public function getValid($entity, $id) {
        return $this->queryDatabase('SELECT * FROM '.$entity.' WHERE status = 1 AND id = :id', [
            ':id' => $id
        ], 'OCR5\Entities\\'. ucfirst($entity));

    }

    public function getPaginatedPosts($page, $limit) {
        $offset = ($page - 1) * $limit;
        return $this->getValids('post', (int)$limit, $offset);
    }

    public function createTable($entity, $valid = false)
    {
        $fqcn = 'OCR5\Entities\\'.ucfirst($entity);
        if (false === class_exists($fqcn)
            || false === in_array('OCR5\Interfaces\EntityInterface', class_implements($fqcn))
                  ) {
            // @todo return error
            $this->addFlash('errorClass', 'Le formulaire ne peut être créé: vérifier que la classe existe et qu\'elle implémente bien EntityInterface.');
            return null;
        }


        $entities = $valid === true ? $this->getValids($entity) : $this->getRequests($entity);

        $form['traductedEntity'] = $fqcn::REQUESTED_TRADUCTION;
        $form['entity'] = $entity;
        $form['type'] = $valid === true ? 'valids' : 'requests';

        $fields = $fqcn::PUBLIC_FIELDS;

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
}
