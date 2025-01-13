<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleCarte.css">
    <!-- Pour les logos menu hamburger-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Site archéologique</title>
</head>
<body>
    <header>
        <nav>
            <ul class="navbar">
                <li class="logoNav"><a href="index.html"><img src="img/logo.png" alt="Logo" class="logoImg"></a></li>
                <input type="checkbox" id="check">
                <span class="menu">
                    <li><a class="lienHeader" href="index.html">Accueil</a></li>
                    <li><a class="lienHeader" href="actualite.html">Actualités</a></li>
                    <li><a class="lienHeader" href="carte.html">Site archéologique</a></li>
                    <li><a class="lienHeader" href="panier.html">Panier</a></li>
                    <li><a class="lienHeader" href="compte.html">Compte</a></li>
                    <label for="check" class="closeMenu"><i class="fas fa-times"></i></label>
                </span>
                <label for="check" class="openMenu"><i class="fas fa-bars"></i></label>
            </ul>
        </nav>
    </header>
    <section id="carte">
        <div id="carteGauche">
            <h2>Région : Rhone-Alpes</h2>
            <div id="carteScroll">
                <a href="" class="contenu">
                    <img id="imgSite" src="img/site5.png" alt="Site archéologique">
                    <div class="contenuTexte">
                        <p id="nom">Nom</p>                        
                        <p id="prix">Prix</p>
                        <p id="horaire">Horaire</p>
                        <p id="adresse">Adresse</p>
                        <p id="description">Description</p>
                    </div>
                </a>

                <!-- Un "a" est un site -->

                <a href="" class="contenu">
                    <img id="imgSite" src="img/site5.png" alt="Site archéologique">
                    <div class="contenuTexte">
                        <p id="nom">Nom</p>                        
                        <p id="prix">Prix</p>
                        <p id="horaire">Horaire</p>
                        <p id="adresse">Adresse</p>
                        <p id="description">Description</p>
                    </div>
                </a>
                <a href="" class="contenu">
                    <img id="imgSite" src="img/site5.png" alt="Site archéologique">
                    <div class="contenuTexte">
                        <p id="nom">Nom</p>                        
                        <p id="prix">Prix</p>
                        <p id="horaire">Horaire</p>
                        <p id="adresse">Adresse</p>
                        <p id="description">Description</p>
                    </div>
                </a>
                <a href="" class="contenu">
                    <img id="imgSite" src="img/site5.png" alt="Site archéologique">
                    <div class="contenuTexte">
                        <p id="nom">Nom</p>                        
                        <p id="prix">Prix</p>
                        <p id="horaire">Horaire</p>
                        <p id="adresse">Adresse</p>
                        <p id="description">Description</p>
                    </div>
                </a>
                <a href="" class="contenu">
                    <img id="imgSite" src="img/site5.png" alt="Site archéologique">
                    <div class="contenuTexte">
                        <p id="nom">Nom</p>                        
                        <p id="prix">Prix</p>
                        <p id="horaire">Horaire</p>
                        <p id="adresse">Adresse</p>
                        <p id="description">Description</p>
                    </div>
                </a>
            </div>
        </div>
        <div id="carteDroite">
            <img src="img/carte.png" alt="Carte régionale">
        </div>
    </section>
        <section id="info">
            <!-- Partie supérieure avec les informations et l'image -->
            <div class="infoDiv">
                <div>
                    <h2 id="nomLieu">Le cirque du fun</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur nemo, porro quia mollitia temporibus ex dolores, corporis quod impedit eligendi maiores. Consequuntur corporis, magnam ad velit cupiditate fugit consequatur recusandae.</p>
                </div>
                <img id="imgSite" src="img/site6.png" alt="">
            </div>
        
            <!-- Contenu additionnel en dessous -->

            <div class="groupesDiv">
                <div class="groupe">
                    <h3>Groupe 1</h3>
                    <p id="nbPersonnes">4/10</p>
                    <p id="date">03/01</p>
                </div>
                <div class="groupe">
                    <h3>Groupe 1</h3>
                    <p id="nbPersonnes">4/10</p>
                    <p id="date">03/01</p>
                </div>
                <div class="groupe">
                    <h3>Groupe 1</h3>
                    <p id="nbPersonnes">4/10</p>
                    <p id="date">03/01</p>
                </div>
            </div>
            <div class="buttons">
                <button id="boutonPrincipal">Ajouter au pack rencontre</button>
                <button id="boutonPrincipal">Ajouter au pack visite</button>
            </div>
        </section>        
        <section id="commentaire">
            <div id="com1">
                <h1>Commentaires pour ce site archéologique</h1>
                <div class="commScroll">
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                    <div class="comm">
                        <img class="imgComm" src="./img/site2.png" alt="Avatar">
                        <div class="infosComm">
                            <h2>Nom de l'utilisateur</h2>
                            <article>Texte du commentaire</article>
                        </div>
                    </div>
                </div>
            </div>
            <div id="com2">
                <div class="comAddEdit">
                    <h1>commentaire pour ce site archéologique</h1>
                    <p>Texte du commentaire</p>
                    <div class="buttonsCom">
                        <button id="boutonSecondaire">Annuler</button>
                        <button id="boutonPrincipal">Poster</button>
                    </div>
                </div>
                <div class="comAddEdit">
                    <h1>Voulez vous modifier ou supprimer votre commentaire ?</h1>
                    <div class="buttonsCom">
                        <button id="boutonSecondaire">Modifier</button>
                        <button id="boutonPrincipal">Supprimer</button>
                    </div>
                </div>
            </div>
        </section>
    <footer>
        <p>&#xA9; 2025 Archeopass. All rights reserved</p>
    </footer>
</body>
</html>