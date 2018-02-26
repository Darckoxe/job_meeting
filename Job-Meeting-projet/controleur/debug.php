<?php
/**
 * Classe qui permet le debug
 */
class Debug
{

  function __construct() {}

  /**
   * Méthode pour afficher les info de permission sur le fichier
   *
   * @param $file string fichier ou dossier à tester
   * @return    string info de permission sur le fichier
   */
  public function fileStat($file)
  {
    $value  = $this->accessRight($file)."<br>\n";
    $value .= $this->getOwner($file)."<br>\n";
    $value .= $this->getGroup($file)."<br>\n";
    return $value;
  }

  /**
   * Méthode qui permet de récuppérer les droits sur un fichier
   *
   * @param $file string fichier à tester
   * @return    info de droit sur fichier
   */
  public function accessRight($file)
  {
    $perms = fileperms($file);

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Lien symbolique
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Régulier
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Dossier
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Caractère spécial
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // pipe FIFO
        $info = 'p';
    } else {
        // Inconnu
        $info = 'u';
    }

    // Autres
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

    // Groupe
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

    // Tout le monde
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));

    return $info;
  }

  /**
   * Méthode qui permet de récuppérer le propriétaire
   *
   * @param $file string fichier à tester
   * @return    String info sur le propriétaire du fichier
   */
  public function getOwner($file)
  {
    $tmp = posix_getpwuid(fileowner($file));
    $owner = "<br>"."Owner :"."<br>\n";
    foreach ($tmp as $key => $value) {
      $owner .= "$key : $value"."<br>\n";
    }
    return $owner;
  }

  /**
   * Méthode qui permet de récuppérer les infos sur le groupe propriétaire
   *
   * @param $file String le fichier à tester
   * @return    String info sur le groupe propriétaire du fichier
   */
  public function getGroup($file)
  {
    $tmp = posix_getgrgid(filegroup($file));
    $group = "<br>"."Group : "."<br>\n";
    foreach ($tmp as $key => $value) {
      if ($key == 'members') {
        foreach ($value as $key2 => $value2) {
          $group .= "$key2 : $value2, ";
        }
      } else {
        $group .= "$key : $value"."<br>\n";
      }
    }
    return $group;
  }
}

 ?>
