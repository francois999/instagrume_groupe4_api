<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerY9QpxxE\App_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerY9QpxxE/App_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerY9QpxxE.legacy');

    return;
}

if (!\class_exists(App_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerY9QpxxE\App_KernelDevDebugContainer::class, App_KernelDevDebugContainer::class, false);
}

return new \ContainerY9QpxxE\App_KernelDevDebugContainer([
    'container.build_hash' => 'Y9QpxxE',
    'container.build_id' => 'dd477c5c',
    'container.build_time' => 1716407398,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerY9QpxxE');
