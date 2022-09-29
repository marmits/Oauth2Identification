<?php
declare(strict_types=1);
namespace Maximo\Adresse\Provider;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

use Maximo\ApiConnector\Api\ConnectorInterface;


abstract class AbstractBuilder
{
    const TYPE_ADRESSE_FACTURATION = "facturation";
    const TYPE_ADRESSE_LIVRAISON = "livraison";

    protected LoggerInterface $logger;
    protected ConnectorInterface $apiConnector;
    protected array $paramsAdresse = [];

    protected array $ApiConnectorParams = [];
    protected string $numclient = "";
    protected array $ClientIdentite = [];
    protected array $ClientAdresse = [];
    protected array $ClientAttributs = [];
    protected array $complement_numero = [];
    protected array $type_voie = [];
    protected array $attributs = [];
    protected array $blocs = [];
    protected array $api76310 = [];
    protected string $type_adresse = "";
    protected array $erreur = [];


    /**
     * @param LoggerInterface $logger
     * @param ConnectorInterface $apiConnector
     * @param array $adresse_params
     */
    public function __construct(LoggerInterface $logger, ConnectorInterface $apiConnector, array $adresse_params)
    {

        $this->logger = $logger;
        $this->apiConnector = $apiConnector;
        $this->setAttributs([]);
        $this->setComplementNumero([]);
        $this->setErreur(true, "Provider construct erreur", "Problèmes au chagement ...");

        $this->setConfigAdresse($adresse_params['adresse_params']);



    }


    ## Retourne les parametres à transmettre pour la creation du adresse javascript
    ###############################################
    /**
     * Retourne les entrées pour le chargement du adresse JS
     * @param array $paramsInput
     * @return array
     */
    public function LoadDatasForJs(array $paramsInput): array{

        $this->setConfigAdresse($paramsInput, ['fromJS']);


        return $this->getConfigAdresse();

    }



    ## getters des bundles dependants
    ###############################################
    /**
     * Retourne le connector d'api
     * @return ConnectorInterface
     */
    public function getApiConnector(): ConnectorInterface{
        return $this->apiConnector;
    }


    ## setters de configuration
    ###############################################

    /**
     * @param array $params
     * @param array $options
     */
    public function setConfigApiConnector(array $params, array $options=[]): AbstractBuilder{
        $this->ApiConnectorParams = [];
        if(!empty(($options))) {
            $this->ApiConnectorParams = array_merge($params, $options);
        }

        return $this;
    }

    public function getConfigApiConnector(): array{
        return $this->ApiConnectorParams;
    }


    public function setConfigAdresse(array $params, array $options=[]): AbstractBuilder{


        //recupere le contenu des listes statiques provenant de l'api

        $this->setConfigApiConnector($params);

        $this->setComplementNumero((array)($this->getApiConnector()->getComplementNumero()));

        $this->setTypeVoie((array)($this->getApiConnector()->getTypeVoie()));

        $this->setAttributs((array)($this->getApiConnector()->getAdresseAttributs()));

        if(!empty($params['blocs'])) {
            $this->setBlocs($params['blocs']);
        }

        if(!empty($params['api76310'])) {
            $this->setApi76310($params['api76310']);
        }



        if(!empty($options)) {


            if(in_array("fromJS", $options, true)) {

                if(isset($params['input_adresseMaximo']['currentClientId'])) {
                    $this->setNumClient($params['input_adresseMaximo']['currentClientId']);
                }
                if(isset($params['input_adresseMaximo']['type_adresse'])) {
                    $this->setTypeAdresse($params['input_adresseMaximo']['type_adresse']);
                }

                if(!empty($params['input_adresseMaximo']['ApiConnectorParams'])) {
                    $this->setConfigApiConnector($this->getConfigApiConnector(), $params['input_adresseMaximo']['ApiConnectorParams']);
                }
            }

        }

        return $this;
    }

    public function getConfigAdresse(): array
    {

        //check valid type_adresse et numcli
        $datas_check_config = $this->check_valid_config();

        $content = [
            'currentClientId' => $this->getNumClient(),
            'type_adresse' => $this->getTypeAdresse(),
            'blocs' => $this->getBlocs(),
            'api76310' => $this->getApi76310(),
            'complement_numero' => $this->getComplementNumero(),
            'type_voie' => $this->getTypeVoie(),
            'erreur' => $datas_check_config["erreur"],
            'erreur_titre' => $datas_check_config["titre"],
            'erreur_message'=> $datas_check_config["message"],
            'connector_params' => $this->getConfigApiConnector()
        ];

        return $content;
    }


    ## setters getters des parametres blocs
    ###############################################


    public function setBlocs(array $val): AbstractBuilder{
        $this->blocs = $val;
        return $this;
    }
    public function getBlocs(): array{
        return $this->blocs;
    }

    public function setApi76310(array $val): AbstractBuilder{
        $this->api76310 = $val;
        return $this;
    }
    public function getApi76310(): array{
        return $this->api76310;
    }



    ## setters getters parametres d'entrées => input_default
    ###############################################
    public function setNumClient($val): AbstractBuilder{
        $this->numclient = $val;
        return $this;
    }
    public function getNumClient(): string{
        return $this->numclient;
    }


    public function setTypeAdresse($val): AbstractBuilder{
        $this->type_adresse = $val;
        return $this;
    }
    public function getTypeAdresse(): string{
        return $this->type_adresse;
    }

    public function setErreur(bool $error, string $titre, string $message): AbstractBuilder{
        $this->erreur = ["erreur" => $error, "titre" => $titre, "message" => $message];
        return $this;
    }
    public function getErreur(): array{
        return $this->erreur;
    }
    

     /**
     * @param array $value
     */
    public function setClientIdentite(array $value): AbstractBuilder{
         $this->ClientIdentite = $value;
         return $this;
     }

    public function getClientIdentite(): array{
        return $this->ClientIdentite;
    }

    public function setComplementNumero(array $value): AbstractBuilder{
        $this->complement_numero = $value;
        return $this;
    }

    public function getComplementNumero(): array{
        return $this->complement_numero;
    }

    public function setTypeVoie(array $value): AbstractBuilder{
        $this->type_voie = $value;
        return $this;
    }

    public function getTypeVoie(): array{
        return $this->type_voie;
    }

    /**
     * @param array $value
     */
    public function setClientAdresse(array $value): AbstractBuilder{
        $this->ClientAdresse = $value;
        return $this;
    }

    public function getClientAdresse(): array{
        return $this->ClientAdresse;
    }

    /**
     * @param array $value
     */
    public function setClientAttributs(array $value): AbstractBuilder{
        $this->ClientAtrtibuts = $value;
        return $this;
    }

    public function getClientAttributs(): array{
        return $this->ClientAtrtibuts;
    }


    /**
     * @param array $value
     */
    public function setAttributs(array $value): AbstractBuilder{
        $this->attributs = $value;
        return $this;
    }

    public function getAttributs(): array{
        return $this->attributs;
    }

    ## api fonctions
    ###############################################
    public function getClientInformation(string $numcli, string $type_adresse,  array $columns): JsonResponse
    {
        $this->setErreur(false, "","");
        $this->setClientIdentite([]);
        $this->setClientAdresse([]);
        $this->setClientAttributs([]);

        // envoie vers le connector le numéro de client et le type d'adresse demandés
        $clientIdentite = (array)($this->getApiConnector()->getClientIdentite($numcli));
        if (array_key_exists("erreur",$clientIdentite)){
            $this->setErreur($clientIdentite["erreur"], $clientIdentite["titre"],$clientIdentite["message"]);

        } else {
            $this->setClientIdentite($clientIdentite);
        }

        $clientAdresse = (array)($this->getApiConnector()->getClientAdresse($numcli, $type_adresse));
        if (array_key_exists("erreur",$clientAdresse)){
            $this->setErreur($clientAdresse["erreur"], $clientAdresse["titre"],$clientAdresse["message"]);
        } else {
            $this->setClientAdresse($clientAdresse);
            $clientAttributs = (array)$this->getApiConnector()->getClientAttributs($numcli);
            if (array_key_exists("erreur",$clientAttributs)){
                $this->setErreur($clientAttributs["erreur"], $clientAttributs["titre"],$clientAttributs["message"]);
            } else {
                $this->setClientAttributs($clientAttributs);
            }
        }
        
        $res = [
            'error' => $this->getErreur(),
            'identite' => $this->getClientIdentite(),
            'adresse' => $this->getClientAdresse(),
            'attributs' => $this->getClientAttributs()
        ];

        return new JsonResponse((array)($res));

    }

    public function getAttributsListe(): JsonResponse
    {
        $this->setErreur(false, "","");
        $listeAttributs =$this->getAttributs();

        if (array_key_exists("erreur",$listeAttributs)){
            $this->setErreur(true, $listeAttributs["titre"],$listeAttributs["message"]);
            $listeAttributs = $this->getErreur();
        }
        else {
            $listeAttributs = (array)$this->formatAttributes();
        }

        return new JsonResponse($listeAttributs);
    }



    // renvoie et complète avec la conf la liste des attributs classés et ordonnés
    private function formatAttributes()
    {


        $sortie['blocs'] = [];

        $list_All_attributs = $this->getAttributs();

        if(count($list_All_attributs) !== 0) {

            try {
                $attributs = array_reduce(array_keys($list_All_attributs), function ($attributs, $key) use ($list_All_attributs) {
                    $attributs_formated['attributes'] = [];
                    $types = array_map(function ($InputAttributs) {
                        $array = $InputAttributs['type_ligne'];
                        return ($array);
                    }, $list_All_attributs);

                    $types = array_unique($types);
                    $blocs = $this->getBlocs();

                    foreach ($types as $type => $val) {
                        foreach ($blocs as $bloc => $valuebloc) {
                            if ($valuebloc['enabled'] === true) {
                                foreach ($list_All_attributs as $key => $datas) {
                                    if ($datas['type_ligne'] === $bloc) {
                                        if ($datas['type_ligne'] === $val) {
                                            $attributs_formated['attributes'][$val][$datas['ordre']] = $datas;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    return $attributs_formated['attributes'];
            });

            } catch (InvalidConfigurationException $e) {
                throw new Exception("Probleme de configuration dans formatAttributes", 0, $e);
            }

            $blocs['key'] = [];
            $blocs['attributes'] = [];
            $blocsEnabled = [];

            foreach ($this->getBlocs() as $ind => $bloc) {
                if ($bloc['enabled'] === true) {
                    if (array_key_exists($ind, $this->getBlocs())) {
                        $blocsEnabled[$ind] = $this->getBlocs()[$ind];
                    }
                }
            }

            foreach ($blocsEnabled as $key => $prorietes) {
                $sortie['blocs'][$key]['titre'] = $prorietes['name'];
                $sortie['blocs'][$key]['order'] = $prorietes['order'];
                foreach ($attributs as $element => $attribut) {
                    if ($element === $key) {
                        $sortie['blocs'][$key]['attributs'] = $attribut;
                    }
                }
            }
        }

        return $sortie;
    }

    // verifie les entrées
    private function check_valid_config(): array{

        $datas_erreur = [
            "erreur" => false,
            "titre" => "",
            "message" => ""
        ];

        //check valid type adresse
        if(!in_array($this->getTypeAdresse(),[self::TYPE_ADRESSE_FACTURATION,self::TYPE_ADRESSE_LIVRAISON])) {
            $raison = $this->getTypeAdresse()." est Invalide";
            if($this->getTypeAdresse() === ""){
                $raison = "Type d'adresse non renseigné";
            }
            $datas_erreur = [
                "erreur" => true,
                "titre" => "Erreur de configuration",
                "message" => $raison . " .\n" . "Le type d'adresse demandé doit être: " . self::TYPE_ADRESSE_FACTURATION . " ou " . self::TYPE_ADRESSE_LIVRAISON
            ];
        }

        //check valid client
        if($datas_erreur["erreur"] === false) {
            $valid_client = json_decode($this->getApiConnector()->validClient($this->getNumClient()), true);
            if (!$valid_client["is_valid_client"]) {
                $datas_erreur = [
                    "erreur" => true,
                    "titre" => "Erreur recherche client",
                    "message" => $valid_client["message"]
                ];
            }
        }
        //check valid blocs params
        if($datas_erreur["erreur"] === false) {
            if(count($this->getBlocs()) === 0) {
                $datas_erreur = [
                    "erreur" => true,
                    "titre" => "Erreur chargement des  blocs",
                    "message" => "Pas de blocs définis dans les paramètres"
                ];
            }
        }

        //check complement_numero, type_voie
        if($datas_erreur["erreur"] === false) {
            $complement_numero = $this->getComplementNumero();
            $type_voie = $this->getTypeVoie();
            if (array_key_exists("erreur",$complement_numero)){
                $datas_erreur = [
                    "erreur" => true,
                    "titre" => $complement_numero["titre"],
                    "message" => $complement_numero["titre"]
                ];
            }
            if (array_key_exists("erreur",$type_voie)){
                $datas_erreur = [
                    "erreur" => true,
                    "titre" => $type_voie["titre"],
                    "message" => $type_voie["message"]
                ];
            }
        }

        $this->setErreur($datas_erreur["erreur"], $datas_erreur["titre"],nl2br($datas_erreur["message"]));

        return $this->getErreur();
    }



}
