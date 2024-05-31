/**
* \page Général 
* 
* \author QUILLAY
* \version 1.5
* \date 5 décembre 2023
*
* Ce programme demande différent age et à la fin en fait la somme
*/

#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>

/**
* \def TMAX
* \brief Taille maximale d’un tableau.
*
* Taille maximale d'une liste d'age.
*/
#define TMAX 5

/**
* \typedef typTab
* \brief Type tableau de TMAX caractères.
*
* Le type typTab permet d'utiliser un tableau pour stocker les ages
*
*/
typedef int typTab[TMAX];

// Prototypes des fonctions
void identité(int *age);
void init(typTab tab);
bool majeur(int age);
int sommeage(typTab tab);

/**
* \fn int main()
* \brief Programme principal.
* \return int : Code de sortie du programme (0 : sortie normale).
* 
* \descF Le programme principal va demander l'age avec la fonction identité puis dire si tu es majeur et enfin écrire la somme des ages
*/
int main()
{
    typTab tab;
    int age;
    init(tab);
    for (int i = 0; i < TMAX; i++)
    {
        identité(&age);
        if (majeur(age)==true)
        {
            printf("tu est majeur\n");
        }
        else
        {
            printf("tu est mineur\n");
        }
        tab[i]=age;
    }
    printf("la somme de tous vos ages est : %d\n", sommeage(tab));
    

    return EXIT_SUCCESS;
}

/**
* \fn void identité(int *age).
* \brief Fonction qui demande l'age
* \param age : paramètre de sortie qui représente l'age à donner
* \return void
*/
void identité(int *age){
    printf("quel est ton age.\n");
    scanf("%d", &*age);
}

/**
* \fn void init(typTab tab).
* \brief Fonction qui initialise la chaine.
* \param tab : paramètre de sortie qui représente le tableau à initialiser.
* \return void
* \descF Met à NULL le tableau passée en paramètre.
*/
void init(typTab tab){
    for (int i = 0; i < TMAX; i++)
    {
        tab[i]=0;
    }
    
}

/**
* \fn bool majeur(int age)
* \brief Fonction qui vérifie si tu as plus de 18 ans
* \param age : paramètre d'entrée qui représente le nombre à tester.
* \return bool : true si age > 18, false sinon.
*/
bool majeur(int age){
    bool vérif = false;
    if (age>=18)
    {
        vérif=true;
    }
    
    return vérif;
}

/**
* \fn int sommeage(typTab tab)
* \brief Calcule les valeurs d'un tableau
* \param tab : paramètre d'entrée qui représente le tableau dont on veut connaître la somme des valeurs.
* \return int : la somme des valeurs du tableau.
*
* \descF Parcours complet du tableau pour compter la somme.
*/
int sommeage(typTab tab){
    int somme = 0;
    for (int i = 0; i < TMAX; i++)
    {
        somme=somme+tab[i];
    }
    return somme;
}