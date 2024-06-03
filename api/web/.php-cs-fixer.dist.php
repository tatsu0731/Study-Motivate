<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/app',
    ])
    ->exclude('var')
    ->exclude('vendor')
    ->exclude('node_modules')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->files()
    ->name('*.php')
    ->notName('*.blade.php');

$config = new PhpCsFixer\Config();
return $config->setFinder($finder)
    ->setRules([
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'], // イコールを揃える
        'blank_line_after_namespace' => true, // namespace後に改行入れる
        'blank_line_after_opening_tag' => true, // 開始タグ後に改行入れる
        'braces' => true,
        'cast_spaces' => true, // 型キャストの前後にスペースを挿入します
        'class_definition' => true,
        'concat_space' => ['spacing' => 'one'], // 結合演算子の前後に空ける空白の指定
        'function_typehint_space' => true, // 関数の返り値の型宣言にスペースが抜けていると補完する
        'no_unused_imports' => true, // 不要なuse削除
        'no_useless_else' => true, // 不要なif-else削除
        'no_useless_return' => true, // メソッド末尾で何も返さないreturnを削除
        'no_trailing_whitespace' => true, // 末尾の空白行を削除する
        'no_whitespace_in_blank_line' => true,
        'ordered_imports' => true,
        'phpdoc_align' => true,
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
    ]);
