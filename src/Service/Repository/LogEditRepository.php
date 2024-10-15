<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: LogEditRepository.php
 * Updated At: 15.10.2024, 16:10
 *
 */

namespace SormModule\Service\Repository;

use SormModule\Service\Entity\LogEdit;
use PDO;

final class LogEditRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Ru: Найти запись по идентификатору.
     * En: Find an entry by ID.
     * @param int $id
     * @return LogEdit|null
     */
    public function find(int $id): ?LogEdit
    {
        $stmt = $this->pdo->prepare('SELECT * FROM logs_edit WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ?? null;
    }

    /**
     * Ru: Найти одну запись по заданным условиям.
     * En: Find a single entry by specified criteria.
     *
     * @param array $criteria
     * @return LogEdit|null
     */
    public function findOneBy(array $criteria): ?LogEdit
    {
        $query = 'SELECT * FROM logs_edit WHERE ';
        $conditions = [];
        $params = [];

        foreach ($criteria as $key => $value) {
            $conditions[] = "$key = :$key";
            $params[$key] = $value;
        }

        $query .= implode(' AND ', $conditions);
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ?? null;
    }

    /**
     * Ru: Найти все записи.
     * En: Find all entries.
     *
     * @return LogEdit[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM logs_edit');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return !empty($results) ? $results : [];
    }

    /**
     * Ru: Сохранить новую запись.
     * En: Save a new entry.
     *
     * @param LogEdit $logEdit
     * @return void
     */
    public function store(LogEdit $logEdit): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO logs_edit (tableName, recordId, action, data, comment, changedAt) VALUES (:table_name, :record_id, :action, :data, :comment, :changed_at)');
        $stmt->execute([
            'table_name' => $logEdit->getTableName(),
            'record_id'  => $logEdit->getRecordId(),
            'action'     => $logEdit->getAction(),
            'data'       => $logEdit->getData(),
            'comment'    => $logEdit->getComment(),
            'changed_at' => $logEdit->getChangedAt(),
        ]);
    }

    /**
     * Ru: Удалить запись по идентификатору.
     * En: Remove an entry by ID.
     *
     * @param int $id
     * @return void
     */
    public function remove(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM logs_edit WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Ru: Обновить существующую запись.
     * En: Update an existing entry.
     *
     * @param LogEdit $logEdit
     * @return void
     */
    public function update(LogEdit $logEdit): void
    {
        $stmt = $this->pdo->prepare('UPDATE logs_edit SET tableName = :table_name, recordId = :record_id, action = :action, data = :data, comment = :comment, changedAt = :changed_at WHERE id = :id');
        $stmt->execute([
            'id'         => $logEdit->getId(),
            'table_name' => $logEdit->getTableName(),
            'record_id'  => $logEdit->getRecordId(),
            'action'     => $logEdit->getAction(),
            'data'       => $logEdit->getData(),
            'comment'    => $logEdit->getComment(),
            'changed_at' => $logEdit->getChangedAt(),
        ]);
    }
}
