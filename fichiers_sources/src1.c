/**
* \page Description générale
* \brief Programme d'un jeu de sudoku
*
* \author VIVION-MICHAUD Enzo
*
* \date 26 novembre 2023
*
* Ce programme permet à l'utilisateur de pouvoir compléter une grille de sudoku pré-définie.
*
*/

#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <unistd.h>


/**
*
* \def N
*
* \brief constante pour déinir la taille d'un bloc de la grille
*
*/

#define N 3

/**
*
* \def TAILLE
*
* \brief constante pour définir la taille maximale de la grille.
*
*/

#define TAILLE N*N

/**
*
* \typedef tGrille
*
* \brief type tableau à 2 dimensions de TAILLE colonnes et TAILLE lignes.
*
* Permet de créer un tableau à 2 dimensions représentant la grille de sudoku.
*
*/

typedef int tGrille[TAILLE][TAILLE];

// Prototypes des fonctions
void chargerGrille(tGrille g);
void afficherGrille(tGrille sudoku);
void saisir(int *valeur_de_test);
bool finie(tGrille sudoku);
bool possible(tGrille sudoku, int ligne, int colonne, int valeur);
int nbElementsColonne(tGrille g, int col);
int nbElementsLigne(tGrille g, int lig);

//programme principal


/**
*
* \fn int main()
*
* \briefF Programme principal
* \return int : EXIT_SUCCESS
* \descF Consiste à faire tourner le programme en lançant les différentes fonctions pour afficher la grille et la modifier.
*
*/

int main() {
    tGrille maGrille = {
        {5, 3, 0, 0, 7, 0, 0, 0, 0},
        {6, 0, 0, 1, 9, 5, 0, 0, 0},
        {0, 9, 8, 0, 0, 0, 0, 6, 0},
        {8, 0, 0, 0, 6, 0, 0, 0, 3},
        {4, 0, 0, 8, 0, 3, 0, 0, 1},
        {7, 0, 0, 0, 2, 0, 0, 0, 6},
        {0, 6, 0, 0, 0, 0, 2, 8, 0},
        {0, 0, 0, 4, 1, 9, 0, 0, 5},
        {0, 0, 0, 0, 8, 0, 0, 7, 9}
    };
    int ligne, colonne, valeur;
    ligne =0;
    colonne =0;
    valeur =0;

    bool fin;
    fin = finie(maGrille);

    while (fin == false) //boucle de jeu qui s'exécute tant que la grille n'est pas complète.
    {

        system("clear");

        afficherGrille(maGrille); // Affiche la grille
        printf("Indices de la case ? ");
        saisir(&ligne);
        saisir(&colonne);
        ligne = ligne -1;  // on enleve 1 car en C un tableau commence à 0 et pas à 1. Sinon on a un décalage.
        colonne = colonne -1;
        if (maGrille[ligne][colonne] != 0) //si la valeur de la case est différente de 0 on ne peut pas rentrer de valeur.
        {
            printf("IMPOSSIBLE, la case n'est pas libre.\n");
            sleep(2);
        }
        else
        {
            printf("Valeur à insérer ? ");
            saisir(&valeur);
            if (possible(maGrille, ligne, colonne, valeur))
            {
                maGrille[ligne][colonne] = valeur;
            }
        }

        system("clear");
        afficherGrille(maGrille);
        fin = finie(maGrille);
    }
    printf("Grille pleine, fin de partie \n");

    return EXIT_SUCCESS;
}

/**
*
* \fn void afficherGrille(tGrille sudoku)
*
* \briefF Fonction qui affiche la grille de sudoku
*
* \param sudoku : La grille de sudoku
* \return void
* \descF Consiste à afficher la grille de sudoku avec les valeurs le cadre et les numéros de ligne et de colonne.
*
*/

void afficherGrille(tGrille sudoku) {
    printf("    1 2 3   4 5 6   7 8 9 \n");
    printf("  +-------+-------+-------+\n");

    // Afficher les lignes de la grille avec les numéros de ligne
    for (int ligne = 0; ligne < TAILLE; ligne++) {
        printf("%d | ", ligne + 1);
        for (int colonne = 0; colonne < TAILLE; colonne++) {
            if (sudoku[ligne][colonne] == 0)
            {
                printf(". ");
            }
            else
            {
                printf("%d ", sudoku[ligne][colonne]);
            }
            
            if ((colonne + 1) % 3 == 0) {
                printf("| ");
            }
        }
        printf("\n");

        // Afficher le cadre entre les lignes de la grille
        if ((ligne + 1) % 3 == 0) {  // permet de pouvoir afficher les "----" et les "+" entre les blocs
            printf("  +");
            for (int colonne = 0; colonne < TAILLE/3; colonne++) {
                for (int i =0; i < 7; i++ ){
                    printf("-");
                }
                printf("+");
            }
            printf("\n");
        }

    }
}


/**
*
* \fn void saisir(int *valeur_de_test)
*
* \briefF Procédure qui récupère la valeur saisie par le joueur.
*
* \param valeur_de_test : variable qui contient la valeur entrée par le joueur.
* \return void
* \descF Consiste à vérifier si la valeur entrée est bien un chiffre compris entre 1 et 9 et si oui l'ajoute à la variable.
*
*/


void saisir(int *valeur_de_test)
{
    bool verification;
    verification = false;
    int valeur_a_convertir;
    char ch[20];  //variable qui contient la saisie de l'utilisateur. Même si ce n'est pas un entier.

    while (!verification)
    {
        scanf("%s",ch);
        if (sscanf(ch, "%d", &valeur_a_convertir) != 0)
        {
            if (valeur_a_convertir > 0 && valeur_a_convertir < TAILLE +1 )
            {
                *valeur_de_test = valeur_a_convertir;
                verification = true;
            }
            else
            {
                printf("Erreur, veuillez entrer un nombre compris entre 1 et 9 ");
                sleep(2);
            }
        }
        else
        {
            printf("Erreur, veuillez entrer un nombre compris entre 1 et 9 ");
            sleep(2);
        }
        
    }


}



/**
*
* \fn bool possible(tGrille sudoku, int ligne, int colonne, int valeur)
*
* \briefF Fonction qui indique si il est possible de rentrer la valeur dans la case souhaitée.
*
* \param sudoku : La grille de sudoku
* \param ligne : valeur de la ligne rentrée par le joueur
* \param colonne : valeur de la colonne rentrée par le joueur
* \param valeur : valeur à rentrer dans la case donnée par le joueur
*
* \return bool : true si la valeur peut être rentrée, false sinon.
*
* \descF Consiste à vérifier si la valeur souhaitée n'est pas déjà dans la ligne, la colonne ou le bloc.
*
*/


bool possible(tGrille sudoku, int ligne, int colonne, int valeur)
{
    // Vérifier la présence de valeur dans la ligne
    for (int x = 0; x < TAILLE; x++) {
        if (sudoku[ligne][x] == valeur) {
            printf("La valeur est déjà dans la ligne \n");
            sleep(2);
            return false;
        }
    }

    // Vérifier la présence de valeur dans la colonne
    for (int y = 0; y < TAILLE; y++) {
        if (sudoku[y][colonne] == valeur) {
            printf("La valeur est déjà dans la colonne \n");
            sleep(2);
            return false;
        }
    }

    // Permet de découper la grille en bloc de 3*3 et de vérifier si valeur n'est pas dans le bloc
    int debutLigne = ligne - ligne % 3;
    int debutColonne = colonne - colonne % 3;
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            if (sudoku[i + debutLigne][j + debutColonne] == valeur) {
                printf("La valeur est déjà dans le bloc \n");
                sleep(2);
                return false;
            }
        }
    }

    // Si la valeur demandée n'est pas présente dans la même ligne, dans la même colonne ou dans le même bloc c'est bon.
    return true;
}


/**
*
* \fn bool finie(tGrille sudoku)
*
* \briefF Fonction qui regarde si la grille est complétée.
*
* \param sudoku : la grille de sudoku
*
* \return bool : true si la grille est complète, false sinon.
*
* \descF Consiste à vérifier si la grille est complète ou non.
*
*/


bool finie(tGrille sudoku)
{
    bool finie;
    finie = true;
    for (int ligne = 0; ligne < N; ligne++) {  //boucle imbriquée de parcours de la grille pour savoir si la grille est complète.
        for (int colonne = 0; colonne < N; colonne++) {
            if (sudoku[ligne][colonne] == 0) {
                finie = false;
            }
        }
    }

    return finie;
}