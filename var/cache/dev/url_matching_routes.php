<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/commentaires' => [
            [['_route' => 'app_commentaire_updatecommentaire', '_controller' => 'App\\Controller\\CommentaireController::updateCommentaire'], null, ['PUT' => 0], null, false, false, null],
            [['_route' => 'app_commentaire_getcomments', '_controller' => 'App\\Controller\\CommentaireController::getComments'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/posts' => [
            [['_route' => 'app_post_createpost', '_controller' => 'App\\Controller\\PostController::createPost'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'app_post_getallposts', '_controller' => 'App\\Controller\\PostController::getAllPosts'], null, ['GET' => 0], null, false, false, null],
        ],
        '/api/modifier' => [[['_route' => 'app_post_updatepost', '_controller' => 'App\\Controller\\PostController::updatePost'], null, ['PUT' => 0], null, false, false, null]],
        '/api/login' => [[['_route' => 'app_user_loguser', '_controller' => 'App\\Controller\\UserController::logUser'], null, ['POST' => 0], null, false, false, null]],
        '/api/inscription' => [[['_route' => 'app_user_createuser', '_controller' => 'App\\Controller\\UserController::createUser'], null, ['POST' => 0], null, false, false, null]],
        '/api/myself' => [[['_route' => 'app_user_getutilisateur', '_controller' => 'App\\Controller\\UserController::getUtilisateur'], null, ['GET' => 0], null, false, false, null]],
        '/api/users' => [[['_route' => 'app_user_getallusers', '_controller' => 'App\\Controller\\UserController::getAllUsers'], null, ['GET' => 0], null, false, false, null]],
        '/api/changerMdp' => [[['_route' => 'app_user_changermdp', '_controller' => 'App\\Controller\\UserController::changerMdp'], null, ['POST' => 0], null, false, false, null]],
        '/api/doc' => [[['_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger_ui'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/(?'
                    .'|commentaire(?'
                        .'|s/([^/]++)(?'
                            .'|(*:77)'
                            .'|(*:84)'
                            .'|(*:91)'
                        .')'
                        .'|/(?'
                            .'|like/([^/]++)(*:116)'
                            .'|dislike/([^/]++)(*:140)'
                        .')'
                    .')'
                    .'|posts/(?'
                        .'|id/([^/]++)(*:170)'
                        .'|([^/]++)(?'
                            .'|(*:189)'
                            .'|/like(*:202)'
                        .')'
                        .'|like/([^/]++)(*:224)'
                        .'|dislike/([^/]++)(*:248)'
                        .'|username/([^/]++)(*:273)'
                    .')'
                    .'|reponse/([^/]++)(*:298)'
                    .'|filtre/([^/]++)(*:321)'
                    .'|deletepost/([^/]++)(*:348)'
                    .'|users/([^/]++)(*:370)'
                    .'|ban/([^/]++)(*:390)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        77 => [[['_route' => 'app_commentaire_getcommentsforpost', '_controller' => 'App\\Controller\\CommentaireController::getCommentsForPost'], ['post'], ['GET' => 0], null, false, true, null]],
        84 => [[['_route' => 'app_commentaire_deletecommentaire', '_controller' => 'App\\Controller\\CommentaireController::deleteCommentaire'], ['id'], ['DELETE' => 0], null, false, true, null]],
        91 => [[['_route' => 'app_commentaire_createcommentaire', '_controller' => 'App\\Controller\\CommentaireController::createCommentaire'], ['post'], ['POST' => 0], null, false, true, null]],
        116 => [[['_route' => 'app_commentaire_addlike', '_controller' => 'App\\Controller\\CommentaireController::addLike'], ['commentaireId'], ['POST' => 0], null, false, true, null]],
        140 => [[['_route' => 'app_commentaire_adddislike', '_controller' => 'App\\Controller\\CommentaireController::addDislike'], ['commentaireId'], ['POST' => 0], null, false, true, null]],
        170 => [[['_route' => 'app_commentaire_getpostidbycommentaireid', '_controller' => 'App\\Controller\\CommentaireController::getPostIdByCommentaireId'], ['commentaireId'], ['GET' => 0], null, false, true, null]],
        189 => [[['_route' => 'app_post_getpostbyid', '_controller' => 'App\\Controller\\PostController::getPostById'], ['id'], ['GET' => 0], null, false, true, null]],
        202 => [[['_route' => 'app_post_getlikecount', '_controller' => 'App\\Controller\\PostController::getLikeCount'], ['postid'], ['GET' => 0], null, false, false, null]],
        224 => [[['_route' => 'app_post_addlike', '_controller' => 'App\\Controller\\PostController::addLike'], ['postId'], ['POST' => 0], null, false, true, null]],
        248 => [[['_route' => 'app_post_adddislike', '_controller' => 'App\\Controller\\PostController::addDislike'], ['postId'], ['POST' => 0], null, false, true, null]],
        273 => [[['_route' => 'app_post_getusernamebypostid', '_controller' => 'App\\Controller\\PostController::getUsernameByPostId'], ['postId'], ['GET' => 0], null, false, true, null]],
        298 => [[['_route' => 'app_commentaire_createreponse', '_controller' => 'App\\Controller\\CommentaireController::createReponse'], ['commentaireId'], ['POST' => 0], null, false, true, null]],
        321 => [[['_route' => 'app_post_getpostbypostname', '_controller' => 'App\\Controller\\PostController::getPostByPostname'], ['username'], ['GET' => 0], null, false, true, null]],
        348 => [[['_route' => 'app_post_deletepost', '_controller' => 'App\\Controller\\PostController::deletePost'], ['id'], ['DELETE' => 0], null, false, true, null]],
        370 => [[['_route' => 'app_user_getuserbyusername', '_controller' => 'App\\Controller\\UserController::getUserByUsername'], ['username'], ['GET' => 0], null, false, true, null]],
        390 => [
            [['_route' => 'app_user_setban', '_controller' => 'App\\Controller\\UserController::setBan'], ['username'], ['PUT' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
