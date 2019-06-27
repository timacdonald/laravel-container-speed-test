<?php

$containerTestStub = file_get_contents(__DIR__.'/container_test_stub.php');
$withoutContainerTestStub = file_get_contents(__DIR__.'/without_container_test_stub.php');
$methodStub = file_get_contents(__DIR__.'/method_stub.php');

$methods = '';

for($i = 0; $i < 15; $i++) {
    $methods .= str_replace('{method_name}', "test_{$i}", $methodStub);
}

for ($i = 0; $i < 100; $i++) {
    $className = "Test{$i}Test";

    $containerTestContents = str_replace('{className}', "Test{$i}Test", $containerTestStub);
    $withoutContainerTestContents = str_replace('{className}', "Test{$i}Test", $withoutContainerTestStub);

    $containerTestContents = str_replace('{body}', $methods, $containerTestContents);
    $withoutContainerTestContents = str_replace('{body}', $methods, $withoutContainerTestContents);

    file_put_contents(__DIR__."/Container/{$className}.php", $containerTestContents);
    file_put_contents(__DIR__."/WithoutContainer/{$className}.php", $withoutContainerTestContents);
}
