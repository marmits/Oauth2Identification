<?php
declare(strict_types=1);
namespace Maximo\Adresse\Provider;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

use Maximo\ApiConnector\Api\ConnectorInterface;


abstract class AbstractBuilder
{
    protected LoggerInterface $logger;
    protected ConnectorInterface $apiConnector;
    protected array $paramsAdresse;


    protected bool $enable_json_file;
    protected array $ApiConnectorParams = [];
    protected string $numclient;
    protected array $ClientIdentite;
    protected array $ClientAdresse;
    protected  array $attributs;

   

    /**
     * @param LoggerInterface $logger
     * @param ConnectorInterface $apiConnector
     * @param array $adresse_params
     */
    public function __construct(LoggerInterface $logger, ConnectorInterface $apiConnector, array $adresse_params)
    {

        $this->logger = $logger;
        $this->apiConnector = $apiConnector;

        $this->setEnableJsonFile(intval($adresse_params['adresse_params']['options']['enable_json_file']));
        $this->setConfigAdresse($adresse_params['adresse_params']);
        if($this->getEnableJsonFile()){
            $this->setClientIdentite($adresse_params['adresse_params']['debug']['file_client']['identite']);
            $this->setAttributs($adresse_params['adresse_params']['debug']['file_attributs']['attributs']);
            $this->setClientAdresse($adresse_params['adresse_params']['debug']['file_client']['adresse']);
        }
    }


    ## Retourne les parametres à transmettre pour la creation du adresse javascript
    ###############################################
    /**
     * Retourne les entrées pour le chargement du adresse JS
     * @param array $paramsInput
     * @return array
     */
    public function LoadDatasForJs(array $paramsInput): array{

        $this->setConfigAdresse($paramsInput,  ['fromJS']);

        $options = [
            'enable_json_file' => $this->getConfigAdresse()['enable_json_file']
        ];

        $configInput = array_merge($options, $this->getConfigApiConnector(),  $this->getConfigAdresse());

        return $configInput;
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
     */
    abstract public function setConfigApiConnector(array $params): AbstractBuilder;

    public function getConfigApiConnector(): array{
        return $this->ApiConnectorParams;
    }

    public function setConfigAdresse(array $params, array $options=[]): AbstractBuilder{
        return $this->setConfigAdresseDefault($params, $options);
    }



    public function getConfigAdresse(): array
    {
        $content = [

            'currentClientId' => $this->getNumClient(),
            'enable_json_file' => $this->getEnableJsonFile()
        ];

        return $content;
    }

    public function setConfigAdresseDefault(array $params, array $options=[]): AbstractBuilder{


        if(!empty($options)) {
            if(in_array("fromJS", $options, true)) {

                if(isset($params['input_adresseMaximo']['currentClientId'])) {
                    $this->setNumClient($params['input_adresseMaximo']['currentClientId']);
                }
                if(!empty($params['input_adresseMaximo']['ApiConnectorParams'])) {
                    $this->setConfigApiConnector($params['input_adresseMaximo']['ApiConnectorParams']);
                }
            }
        }
        return $this;
    }




    ## setters getters des parametres options
    ###############################################


    public function setEnableJsonFile($val): bool{
        $this->enable_json_file = boolval($val);

        return $this->enable_json_file;
    }
    public function getEnableJsonFile(): bool{
        return $this->enable_json_file;
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
    public function setAttributs(array $value): AbstractBuilder{
        $this->attributs = $value;
        return $this;
    }

    public function getAttributs(): array{
        return $this->attributs;
    }

    ## api fonctions
    ###############################################
    public function getClientInformation(string $numcli, array $columns): JsonResponse
    {
        $res = [];
        if($this->getEnableJsonFile()) {
            $res = ['identite' => $this->getClientIdentite(),'adresse' => $this->getClientAdresse()];
            return new JsonResponse((array)($res));
        }
        # return new JsonResponse((array)($this->getApiConnector()->getClientInformation($numcli, $columns)));
    }

    public function getAttributsListe(): JsonResponse
    {
        $res = [];
        if($this->getEnableJsonFile()) {
            $res = ['attributs' => $this->getAttributs()];
            return new JsonResponse((array)($res));
        }
        # return new JsonResponse((array)($this->getApiConnector()->getClientInformation($numcli, $columns)));
    }



}
