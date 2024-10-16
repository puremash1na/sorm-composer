<?php
/*
 * Copyright (c) 2024 - 2024, WebHost1, LLC. All rights reserved.
 * Author: epilepticmane
 * File: LogEdit.php
 * Updated At: 16.10.2024, 14:39
 *
 */

namespace SormModule\Service\Entity;

final class LogEdit
{
    private int $id;
    private string $tableName;
    private string $recordId;
    private string $action;
    private ?array $data;
    private string $comment;
    private string $changedAt;
    private int    $sendedAtSorm;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getRecordId(): string
    {
        return $this->recordId;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getChangedAt(): string
    {
        return $this->changedAt;
    }

    /**
     * @return int
     */
    public function getSendedAtSorm(): int
    {
        return $this->sendedAtSorm;
    }
    public function setId(int $id): LogEdit
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @param string $tableName
     * @return LogEdit
     */
    public function setTableName(string $tableName): LogEdit
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @param string $recordId
     * @return LogEdit
     */
    public function setRecordId(string $recordId): LogEdit
    {
        $this->recordId = $recordId;
        return $this;
    }

    /**
     * @param string $action
     * @return LogEdit
     */
    public function setAction(string $action): LogEdit
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param array|null $data
     * @return LogEdit
     */
    public function setData(?array $data): LogEdit
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $comment
     * @return LogEdit
     */
    public function setComment(string $comment): LogEdit
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @param string $changedAt
     * @return LogEdit
     */
    public function setChangedAt(string $changedAt): LogEdit
    {
        $this->changedAt = $changedAt;
        return $this;
    }

    /**
     * @param int $sendedAtSorm
     * @return LogEdit
     */
    public function setSendedAtSorm(int $sendedAtSorm): LogEdit
    {
        $this->sendedAtSorm = $sendedAtSorm;
        return $this;
    }
}