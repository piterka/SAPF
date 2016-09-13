<?php

namespace SAPF\Repository\Extension;

/* ---------------------------------------------------
 *  MySQL create table for Entity Relations system
 * ----------------------------------------------------

  CREATE TABLE IF NOT EXISTS `entityRelations` (
  `entityRelations_id` int(11) NOT NULL,
  `entityRelations_key` varchar(128) CHARACTER SET utf8 NOT NULL,
  `entityRelations_master` int(11) NOT NULL,
  `entityRelations_masterRepo` varchar(128) CHARACTER SET utf8 NOT NULL,
  `entityRelations_slave` int(11) NOT NULL,
  `entityRelations_slaveRepo` varchar(128) CHARACTER SET utf8 NOT NULL,
  `entityRelations_order` int(11) NOT NULL,
  `entityRelations_data` text CHARACTER SET utf8 NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

  ALTER TABLE `entityRelations`
  ADD PRIMARY KEY (`entityRelations_id`), ADD KEY `entityRelations_key` (`entityRelations_key`), ADD KEY `entityRelations_master` (`entityRelations_master`), ADD KEY `entityRelations_masterRepo` (`entityRelations_masterRepo`), ADD KEY `entityRelations_slave` (`entityRelations_slave`), ADD KEY `entityRelations_slaveRepo` (`entityRelations_slaveRepo`), ADD KEY `entityRelations_order` (`entityRelations_order`);
  ALTER TABLE `entityRelations`
  MODIFY `entityRelations_id` int(11) NOT NULL AUTO_INCREMENT;

 * ----------------------------------------------------
 */

class EntityRelation extends ExtensionAbstract
{

    public function __construct()
    {
        parent::__construct("entityRelation");
    }

    public function fetcher(array &$param, \SAPF\Database\Model &$model)
    {
        if (isset($param['entityRelations_master'])) {
            if (isset($param['entityRelations_master']['id'])) {
                $model->joinLeft('entityRelations', [
                    'entityRelations_master'     => $param['entityRelations_master']['id'],
                    'entityRelations_masterRepo' => $param['entityRelations_master']['repo'],
                    'entityRelations_slave'      => $this->getRepository()->getPrimaryKeyName(),
                    'entityRelations_slaveRepo'  => $this->getRepository()->getName(),
                ]);
            }
            else {
                $model->joinLeft('entityRelations', [
                    'entityRelations_master'    => $param['entityRelations_master'],
                    'entityRelations_slave'     => $this->getRepository()->getPrimaryKeyName(),
                    'entityRelations_slaveRepo' => $this->getRepository()->getName(),
                ]);
            }
            $param['entityRelations_slave !='] = null;
            unset($param['entityRelations_master']);
        }
        elseif (isset($param['entityRelations_slave'])) {
            if (isset($param['entityRelations_slave']['id'])) {
                $model->joinLeft('entityRelations', [
                    'entityRelations_slave'      => $param['entityRelations_slave']['id'],
                    'entityRelations_slaveRepo'  => $param['entityRelations_slave']['repo'],
                    'entityRelations_master'     => $this->getRepository()->getPrimaryKeyName(),
                    'entityRelations_masterRepo' => $this->getRepository()->getName(),
                ]);
            }
            else {
                $model->joinLeft('entityRelations', [
                    'entityRelations_slave'      => $param['entityRelations_slave'],
                    'entityRelations_master'     => $this->getRepository()->getPrimaryKeyName(),
                    'entityRelations_masterRepo' => $this->getRepository()->getName(),
                ]);
            }
            $param['entityRelations_master !='] = null;
            unset($param['entityRelations_slave']);
        }
    }

    public function remove(array $entity)
    {
        // remove relations mastered by entity
        $this->getDb()->delete('entityRelations', [
            'entityRelations_master'     => $entity[$this->getRepository()->getPrimaryKeyName()],
            'entityRelations_masterRepo' => $this->getRepository()->getName(),
        ]);
    }

    public function save(array $entity)
    {
        if (!isset($entity['entityRelations'])) {
            return;
        }

        if (is_array($entity['entityRelations'])) {
            // clear old relations
            $this->getDb()->delete('entityRelations', [
                'entityRelations_master'     => $entity[$this->getRepository()->getPrimaryKeyName()],
                'entityRelations_masterRepo' => $this->getRepository()->getName(),
            ]);
            // generate new relations
            $data = [];
            foreach ($entity['entityRelations'] as $relKey => $relations) {
                if (!is_array($relations)) {
                    throw new ExtensionException("entityRelations['" . $relKey . "'] must be array");
                }
                foreach ($relations as $slave) {
                    if (!isset($slave['id']) || !isset($slave['repo'])) {
                        throw new ExtensionException("entityRelations['" . $relKey . "'] entry must be array with keys \"id\" and \"repo\"");
                    }
                    $data[] = [
                        'entityRelations_key'        => $relKey,
                        'entityRelations_master'     => $entity[$this->getRepository()->getPrimaryKeyName()],
                        'entityRelations_masterRepo' => $this->getRepository()->getName(),
                        'entityRelations_slave'      => $slave['id'],
                        'entityRelations_slaveRepo'  => $slave['repo'],
                        'entityRelations_data'       => isset($slave['data']) ? json_encode($slave['data']) : json_encode([]),
                        'entityRelations_order'      => isset($slave['order']) ? $slave['order'] : 0,
                    ];
                }
            }
            // save
            $this->getDb()->insert('entityRelations', $data);
        }
        else {
            throw new ExtensionException("entityRelations must be array");
        }
    }

    public function tuple2entity(array $param, array $request, array &$tuple)
    {
        if (!isset($request['entityRelations'])) {
            return;
        }
        $relations                = $this->getDb()->fetchAll('entityRelations', '*', [
            'entityRelations_master'     => $tuple[$this->getRepository()->getPrimaryKeyName()],
            'entityRelations_masterRepo' => $this->getRepository()->getName(),
            'order'                      => ['entityRelations_order DESC', 'entityRelations_id ASC'],
        ]);
        $tuple['entityRelations'] = [];
        foreach ($relations as $rel) {
            if (!is_array($tuple['entityRelations'][$rel['entityRelations_key']])) {
                $tuple['entityRelations'][$rel['entityRelations_key']] = [];
            }
            $tuple['entityRelations'][$rel['entityRelations_key']][] = [
                'id'    => $rel['entityRelations_slave'],
                'repo'  => $rel['entityRelations_slaveRepo'],
                'data'  => json_decode($rel['entityRelations_data'], true),
                'order' => $rel['entityRelations_order'],
            ];
        }
    }

    public function tuples2entities(array $param, array $request, array &$tuples)
    {
        if (!isset($request['entityRelations'])) {
            return;
        }
        // get all tuples ids
        $ids = [];
        foreach ($tuples as $t) {
            $ids[] = $t[$this->getRepository()->getPrimaryKeyName()];
        }
        // fetch relations
        $relations         = $this->getDb()->fetchAll('entityRelations', '*', [
            'entityRelations_master'     => $ids,
            'entityRelations_masterRepo' => $this->getRepository()->getName(),
            'order'                      => ['entityRelations_order DESC', 'entityRelations_id ASC'],
        ]);
        // group relations
        $relationsGroupped = [];
        foreach ($relations as $rel) {
            if (!is_array($relationsGroupped[$rel['entityRelations_master']])) {
                $relationsGroupped[$rel['entityRelations_master']] = [];
            }
            if (!is_array($relationsGroupped[$rel['entityRelations_master']][$rel['entityRelations_key']])) {
                $relationsGroupped[$rel['entityRelations_master']][$rel['entityRelations_key']] = [];
            }
            $relationsGroupped[$rel['entityRelations_master']][$rel['entityRelations_key']][] = [
                'id'    => $rel['entityRelations_slave'],
                'repo'  => $rel['entityRelations_slaveRepo'],
                'data'  => json_decode($rel['entityRelations_data'], true),
                'order' => $rel['entityRelations_order'],
            ];
        }
        // inject into tuples
        foreach ($tuples as $k => $t) {
            if (isset($relationsGroupped[$this->getRepository()->getPrimaryKeyName()])) {
                $tuples[$k]['entityRelations'] = $relationsGroupped[$this->getRepository()->getPrimaryKeyName()];
            }
        }
    }

}
