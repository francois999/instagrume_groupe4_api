<?php

namespace Proxies\__CG__\App\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Commentaire extends \App\Entity\Commentaire implements \Doctrine\ORM\Proxy\InternalProxy
{
    use \Symfony\Component\VarExporter\LazyGhostTrait {
        initializeLazyObject as __load;
        setLazyObjectAsInitialized as public __setInitialized;
        isLazyObjectInitialized as private;
        createLazyGhost as private;
        resetLazyObject as private;
    }

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'dislikes' => [parent::class, 'dislikes', null],
        "\0".parent::class."\0".'id' => [parent::class, 'id', null],
        "\0".parent::class."\0".'likes' => [parent::class, 'likes', null],
        "\0".parent::class."\0".'parent' => [parent::class, 'parent', null],
        "\0".parent::class."\0".'post' => [parent::class, 'post', null],
        "\0".parent::class."\0".'responses' => [parent::class, 'responses', null],
        "\0".parent::class."\0".'user' => [parent::class, 'user', null],
        "\0".parent::class."\0".'valeur' => [parent::class, 'valeur', null],
        'dislikes' => [parent::class, 'dislikes', null],
        'id' => [parent::class, 'id', null],
        'likes' => [parent::class, 'likes', null],
        'parent' => [parent::class, 'parent', null],
        'post' => [parent::class, 'post', null],
        'responses' => [parent::class, 'responses', null],
        'user' => [parent::class, 'user', null],
        'valeur' => [parent::class, 'valeur', null],
    ];

    public function __construct(?\Closure $initializer = null, ?\Closure $cloner = null)
    {
        if ($cloner !== null) {
            return;
        }

        self::createLazyGhost($initializer, [
            "\0".parent::class."\0".'id' => true,
        ], $this);
    }

    public function __isInitialized(): bool
    {
        return isset($this->lazyObjectState) && $this->isLazyObjectInitialized();
    }

    public function __serialize(): array
    {
        $properties = (array) $this;
        unset($properties["\0" . self::class . "\0lazyObjectState"], $properties['__isCloning']);

        return $properties;
    }
}