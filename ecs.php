<?php

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    // A. full sets
    $ecsConfig->sets([SetList::ARRAY]);
    $ecsConfig->sets([SetList::NAMESPACES]);
    $ecsConfig->sets([SetList::PSR_12]);
    $ecsConfig->sets([SetList::SPACES]);
    $ecsConfig->sets([SetList::CLEAN_CODE]);
    $ecsConfig->sets([SetList::STRICT]);

    // B. standalone rule
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);

    $ecsConfig->paths([__DIR__ . '/app']);

    // file extensions to scan [default: [php]]
    $ecsConfig->fileExtensions(['php', 'phpt']);

    // configure cache paths & namespace - useful for Gitlab CI caching, where getcwd() produces always different path
    // [default: sys_get_temp_dir() . '/_changed_files_detector_tests']
    $ecsConfig->cacheDirectory('temp/.ecs/');

    // indent and tabs/spaces [default: spaces]
    $ecsConfig->indentation('spaces');
};