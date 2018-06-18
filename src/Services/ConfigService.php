<?php

namespace Railroad\Railforums\Services;

class ConfigService
{
	/**
     * @var string
     */
    public static $databaseConnectionName;
    
    /**
     * @var string
     */
    public static $connectionMaskPrefix;

    /**
     * @var string
     */
    public static $dataMode;

    /**
     * @var string
     */
    public static $tablePrefix;

    /**
     * @var string
     */
    public static $authorTableName;

    /**
     * @var string
     */
    public static $authorTableIdColumnName;

    /**
     * @var string
     */
    public static $authorTableDisplayNameColumnName;

    /**
     * @var string
     */
    public static $tableCategories;

    /**
     * @var string
     */
    public static $tableThreads;

    /**
     * @var string
     */
    public static $tableThreadFollows;

    /**
     * @var string
     */
    public static $tableThreadReads;

    /**
     * @var string
     */
    public static $tablePosts;

    /**
     * @var string
     */
    public static $tablePostLikes;

    /**
     * @var string
     */
    public static $tablePostReports;

    /**
     * @var string
     */
    public static $tablePostReplies;

    /**
     * @var string
     */
    public static $tableSearchIndexes;

    /**
     * @var array
     */
    public static $controllerMiddleware;
}