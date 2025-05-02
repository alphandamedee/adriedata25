<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour gérer la FAQ (Foire Aux Questions)
 */
class FaqController extends AbstractController
{
    /**
     * Affiche la page de FAQ avec les questions et réponses organisées par catégories
     *
     * @return Response Page de la FAQ
     */
    #[Route('/faq', name: 'app_faq')]
    public function index(): Response
    {
        // Structure des questions/réponses organisée par catégories
        $faqs = [
            [
                'categorie' => 'Général',
                'questions' => [
                    [
                        'question' => 'Qu\'est-ce qu\'AdrieData ?',
                        'reponse' => 'AdrieData est une application de gestion d\'inventaire et de suivi des interventions sur le matériel informatique.'
                    ],
                    [
                        'question' => 'Comment accéder à mon compte ?',
                        'reponse' => 'Vous pouvez accéder à votre compte en utilisant votre adresse email et votre mot de passe sur la page de connexion.'
                    ]
                ]
            ],
            [
                'categorie' => 'Interventions',
                'questions' => [
                    [
                        'question' => 'Comment créer une nouvelle intervention ?',
                        'reponse' => 'Pour créer une nouvelle intervention, accédez à la page des produits, sélectionnez un produit puis cliquez sur "Nouvelle Intervention".'
                    ],
                    [
                        'question' => 'Comment modifier une intervention existante ?',
                        'reponse' => 'Les interventions ne peuvent pas être modifiées une fois enregistrées pour des raisons de traçabilité.'
                    ],
                    [
                        'question' => 'Comment imprimer une fiche d\'intervention ?',
                        'reponse' => 'Après avoir enregistré une intervention, un bouton "Imprimer" apparaît vous permettant de générer un PDF de la fiche.'
                    ]
                ]
            ],
            [
                'categorie' => 'Produits',
                'questions' => [
                    [
                        'question' => 'Comment ajouter un nouveau produit ?',
                        'reponse' => 'Dans la section Produits, cliquez sur le bouton "Ajouter un produit" et remplissez le formulaire avec les informations nécessaires.'
                    ],
                    [
                        'question' => 'Comment scanner un code-barres ?',
                        'reponse' => 'Utilisez la fonction de scan en cliquant sur l\'icône de la caméra dans les champs de code-barres. Assurez-vous d\'avoir autorisé l\'accès à votre caméra.'
                    ],
                    [
                        'question' => 'Comment modifier les informations d\'un produit ?',
                        'reponse' => 'Dans la liste des produits, cliquez sur le bouton "Modifier" du produit concerné pour accéder au formulaire d\'édition.'
                    ]
                ]
            ],
            [
                'categorie' => 'Compte utilisateur',
                'questions' => [
                    [
                        'question' => 'Comment modifier mon mot de passe ?',
                        'reponse' => 'Accédez à votre profil en cliquant sur votre nom en haut à droite, puis utilisez l\'option "Modifier le mot de passe".'
                    ],
                    [
                        'question' => 'J\'ai oublié mon mot de passe, que faire ?',
                        'reponse' => 'Sur la page de connexion, cliquez sur "Mot de passe oublié" et suivez les instructions envoyées par email.'
                    ],
                    [
                        'question' => 'Comment changer ma photo de profil ?',
                        'reponse' => 'Dans votre profil, cliquez sur "Modifier la photo" et téléchargez une nouvelle image.'
                    ]
                ]
            ]
        ];

        // Rendu de la vue avec les données de la FAQ
        return $this->render('faq/index.html.twig', [
            'faqs' => $faqs
        ]);
    }
}
