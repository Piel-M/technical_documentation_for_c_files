/**
 * \file calculatrice.c
 * \brief Programme simple de calculatrice avec les opérations de base.
 * 
 * Ce programme permet à l'utilisateur de saisir deux valeurs et de choisir
 * une opération parmi la somme, la division, la soustraction et la multiplication.
 * Le résultat de l'opération choisie est affiché, ainsi que l'information sur
 * laquelle des deux valeurs est la plus grande.
 *
 * \author John PAILLE
 * \date 4 décembre 2023
 */

#include <stdlib.h>
#include <stdio.h>
#include <math.h>
#include <stdbool.h>

#define N 3
typedef float tableau[N];

// Prototypes des fonctions
float somme(float v1, float v2);
float division(float v1, float v2);
float soustraction(float v1, float v2);
float multiplication(float v1, float v2);

/**
* \fn int main()
* \briefF Fonction principale du programme.
* \return EXIT_SUCCESS si le programme s'est exécuté avec succès.
*/
int main(){

    // Déclaration des variables
    tableau tab;
    float resultat;
    bool v1_max;
    int choix;
    float v1;
    float v2;
    tab[1]=0;
    tab[2]=0;

    // Saisie des données
    printf("Saisir 2 nombres décimaux\n");
    scanf("%f", &v1);
    scanf("%f", &v2);
    printf("Choisir une Opération\n");
    printf("Somme (1), Division (2), Soustraction (3), Multiplication (4)\n");
    scanf("%d", &choix);

    tab[0]=v1;
    tab[1]=v2;

    // Sélectionner l'opération choisie par l'utilisateur
    switch (choix){
        case 1: resultat = somme(v1, v2);
            break;
        case 2: resultat = division(v1, v2);
            break;
        case 3: resultat = soustraction(v1, v2);
            break;
        case 4: resultat = multiplication(v1, v2);
            break;
        default: printf("Erreur : Choix invalide\n");
            return EXIT_FAILURE;
    }

    // Initialisation du résultat
    tab[2]=resultat;

    // Affichage du tableau

    for (int i=0; i<N; i++){
        if (i==0){
            printf("Valeur 1 = %.2f\n", tab[i]);
        }

            if (i==1){
            printf("Valeur 2 = %.2f\n", tab[i]);
        }

            if (i==2){
            printf("Résultat = %.2f\n", resultat);
        }

    }
    

    // Vérification de la valeur maximale
    if (v1 > v2) {
        v1_max = true;
    }

    // Affichage de l'information sur la valeur maximale
    if (v1_max) {
        printf("Valeur 1 > Valeur 2\n");
    }

    return EXIT_SUCCESS;
}

// Implémentation des fonctions

/**
* \fn float somme(float v1, float v2)
* \briefF Effectue l'opération de somme entre deux valeurs.
* \param v1 La première valeur.
* \param v2 La deuxième valeur.
* \return float : Le résultat de la somme.
*/
float somme(float v1, float v2){
    return (v1 + v2);
}

/**
* \fn float division(float v1, float v2)
* \briefF Effectue l'opération de division entre deux valeurs.
* \param v1 La première valeur.
* \param v2 La deuxième valeur (non nulle).
* \return float : Le résultat de la division.
*/
float division(float v1, float v2){
    return (v1 / v2);
}

/**
* \fn float soustraction
* \briefF Effectue l'opération de soustraction entre deux valeurs.
* \param v1 La première valeur.
* \param v2 La deuxième valeur.
* \return float : Le résultat de la soustraction (v1 - v2).
*/
float soustraction(float v1, float v2){
    return (v1 - v2);
}

/**
* \fn float multiplication(float v1, float v2)
* \briefF Effectue l'opération de multiplication entre deux valeurs.
* \param v1 La première valeur.
* \param v2 La deuxième valeur.
* \return float : Le résultat de la multiplication.
*/
float multiplication(float v1, float v2){
    return (v1 * v2);
}
