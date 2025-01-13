import dao from "./dao.js";

class Lieu {
    nom;
    prix;
    horaire;
    link_img;
    adresse;
    description;

    constructor(prenom_obj = '',nomLieu = null,prixLieu = null,horaireLieu = null,link_imgLieu = null,adresseLieu = null,descrLieu = null){
        if(typeof prenom_obj == 'object'){
            Object.assign(this,prenom_obj);
        }else{
        this.nom = nomLieu;
        this.adresse = adresseLieu;
        this.prix = prixLieu;
        this.horaire = horaireLieu;
        this.link_img = link_imgLieu;
        this.description = descrLieu;
        }
    }

    static readLieu(nomLieu, onAnswer){
        const params = {
            'lieu' : nomLieu
        };
        dao.queryGetLieu(params,function(answer){
            const liste = [];

            if('error' in answer){
                alert("Error : "+answer.error);
                
            }else{
                for(let lieu of answer.lieus){
                    liste.push(new Lieu(lieu));
                };

                onAnswer(liste);
            }

        });

    }
}

class PackRencontre{
    nom; // nom de la rencontre
    nbpersonne; // affichage nbPersonne/30
    date; // date de la rencontre
}

export {PackRencontre,Lieu};