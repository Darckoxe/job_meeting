<?php

include_once __DIR__.'/../modele/dao/dao_2016.php';

/**
 * Controlleur de vérification de l'entré utilisateur du code captcha.
 * Utilisation de l'api google recaptcha.
 * voir : https://www.google.com/recaptcha/intro/index.html pour plus d'info
 */
class ControleurCaptcha {
  /**
   * permet de stocker un code correspondant à l'entré de l'utilisateur
   * @var String
   */
  private $inputUser;

/**
 * clé privée à conserver secrète pemetant de communiquer avec l'api de Google
 * @var String
 */
  private $key;

  /**
   * clé publique affiché sur le site nécéssaire à l'affichage du captcha
   * @var String
   */
  private $siteKey;

  /**
   * Tableau contenant l'url et le port du proxy
   * @var array
   */
  private $proxy;

  /**
   * Url de l'api google
   * @var String
   */
  private $url;

  /**
   * Permet créer un un controleur pour la captcha.
   * les entrées du code de l'entrée utilisateur sont récupéré dans le post.
   * Le code pour communiquer avec l'api est pour le moment entré en dur est pour le moment entré en brut. (export possible vers la bd ou un fichier externe)
   */
  function __construct() {
    $dao = new Dao_2016();
    if (isset($_POST['g-recaptcha-response'])) {
      $this->inputUser = $_POST['g-recaptcha-response'];
    } else {
      $this->inputUser = null;
    }
    $tmp = $dao->getRecaptchaInfo();
    $this->key = $tmp['secretKey'];
    $this->siteKey = $tmp['siteKey'];
    $this->url = $tmp['url'];
    $this->proxy = $dao->getProxySetting();
  }

  /**
   * récuppérer la clé afin d'afficher le captcha
   * @return String clé du site
   */
  public function getSiteKey()
  {
    return $this->siteKey;
  }

  /**
   * méthode qui permet de vérifier si l'utilisateur à bien réussit l'entré de la captcha et n'est pas un robot.
   * Elle utilise deux méthodes pour envoyer la méthode post au serveur.
   * @return bool test si l'utilisateur ne s'est pas trompé.
   */
  public function verifyCaptcha() {
    $response = null;
    //paramètre de la requète à envoyer au serveur
    $data = array('secret' => "$this->key", 'response' => "$this->inputUser");
    $error_msg = "Le serveur est dans l'incapacité de vérifier le captcha. Veuillez réessayer plus tard. Si le problème persiste, veuillez à contacter l'administrateur. Merci.";
    //utilisation de curl si présent
    if (function_exists('curl_version')) {
      $response = $this->verifyCaptchaCurl($this->url, $data, $this->proxy['url'], $this->proxy['port']);
    } else {
      if (!is_null($this->proxy['url']) && is_null($this->proxy['port'])) {
        $response = $this->verifyCaptchaFGC($this->url, $data);
      } else {
        throw new Exception("vérifier si la bibliothèque curl est installé ou si les paramètre du proxy sont bien configuré", 1);
        $_SESSION['fail'] = $error_msg;
      }
    }
    if (is_null($response)) {
      throw new Exception("La requête pour récupérer le résultat de la captcha à échoué.", 1);
      $_SESSION['fail'] = $error_msg;
    }
    $json = json_decode($response);
    return $json->success;
  }

  /**
   * Méthode qui permet de forger et d'envoyer une requête post au serveur en utilisant "file_get_contents" (old)
   * @param  String $url  Url de destination
   * @param  array $data paramètre à envoyer en post
   * @return String       retourne un json contenant les réponses du serveur
   */
  public function verifyCaptchaFGC($url, $data) {
    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $this->inputUser = null;
    if (empty($response) || is_null($response)) {
        return null;
    }
    return $response;
  }

  /**
   * Méthode qui permet de forger et d'envoyer une requête post au serveur en utilisant curl (non forcément présent)
   * @param  String $url  Url de destination
   * @param  array $data paramètre à envoyer en post
   * @param  String $proxy url du proxy (null si pas de proxy)
   * @param  int  $proxy_port port du proxy
   * @return String       retourne un json contenant les réponses du serveur
   */
  public function verifyCaptchaCurl($url, $data, $proxy, $proxy_port) {
    $curl = curl_init($url);
    if (!is_null($proxy)) {
      curl_setopt($curl, CURLOPT_PROXY, $proxy);
      curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
    }
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    if (empty($response) || is_null($response)) {
      return null;
    }
    return $response;
  }
}

 ?>
