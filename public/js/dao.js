/**
 * Lance les requêtes pour pouvoir acquérir les lieux sur la carte
 * et lance les requêtes vers le php toujours pour obtenir les groupes présents 
 * sur un lieu qui sont disponibles !
 */
const dao = {
    // param = lieu(région)
    // onAnswer callback lancé lorsque le json est recu
    queryGetLieu: function (params, onAnswer){
        const queryString = new URLSearchParams();

        for (let param in params){
            queryString.append(param,params[param]);
        }

        const URL = 'public/api/carteGetLieu.php?'+queryString;

        fetch(URL).then(result => {
            if(result.ok && result.status == 200){
                console.log(result); // on affiche le résultat dans la console
                return result.json(); // renvoie à la callback le json
            }else{
                throw new Error('Erreur serveur', {cause : result});
            }
        }).then(objJSON => {
            console.log(objJSON); // objet json pour le debogage
            onAnswer(objJSON);
        });
    },
    // param = lieu(région)
    // onAnswer callback lancé lorsque le json est recu
    queryGetGroupe: function(params, onAnswer){
        const queryString = new URLSearchParams();
        
        for (let param in params){
            queryString.append(param,params[param]);
        }

        const URL = 'public/api/carteGetGroupe.php?'+queryString;
        
        fetch(URL).then(result => {
            if(result.ok && result.status == 200){
                console.log(result); // on affiche le résultat dans la console
                return result.json(); // renvoie à la callback le json
            }else{
                throw new Error('Erreur serveur', {cause : result});
            }
        }).then(objJSON => {
            console.log(objJSON); // objet json pour le debogage
            onAnswer(objJSON);
        });
    }
}

export default dao;