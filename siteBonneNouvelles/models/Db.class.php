<?php
class Db
{
    private static $instance = null;
    private $_db;

    private function __construct()
    {
        try {
    //        $this->_db = new PDO('mysql:host=localhost;dbname=bdbn;charset=utf8', 'root', '');
    //        $this->_db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$_bd = parse_url(getenv("postgres://zwxbsxbapiwseu:18768c9776bb158c3ae95e737badf5f83fd0cc225b6c7312baf9a65586c3a391@ec2-18-203-62-227.eu-west-1.compute.amazonaws.com:5432/d18grda98estft"));

	$this->bd = new PDO("pgsql:" . sprintf(
    "host=%s;port=%s;user=%s;password=%s;dbname=%s",
    $db["host"],
    $db["port"],
    $db["user"],
    $db["pass"],
    ltrim($db["path"], "/")
));
        } 
		catch (PDOException $e) {
		    die('Erreur de connexion à la base de données : '.$e->getMessage());
        }

		catch (PDOException $e) {
		    die('Erreur de connexion à la base de données : '.$e->getMessage());
        }
    }

	# Pattern Singleton
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Db();
        }
        return self::$instance;
    }

    # Fonction qui exécute un SELECT dans la table des livres
    # et qui renvoie un tableau d'objet(s) de la classe Livre
    public function select_livres($keyword='') {
        # Définition du query et préparation
        if ($keyword != '') {
            $keyword = str_replace("%", "\%", $keyword);
            $query = "SELECT * FROM livres WHERE titre LIKE :keyword COLLATE utf8_bin ORDER BY no DESC ";
            $ps = $this->_db->prepare($query);
            # Le bindValue se charge de quoter proprement les valeurs des variables sql
            $ps->bindValue(':keyword',"%$keyword%");
        } else {
            $query = 'SELECT * FROM livres ORDER BY no DESC';
            $ps = $this->_db->prepare($query);
        }

        # Exécution du prepared statement
        $ps->execute();

        $tableau = array();
        # Parcours de l'ensemble des résultats
        # Construction d'un tableau d'objet(s) de la classe Livre
        # Si le tableau est vide, on ne rentre pas dans le while
		//var_dump($ps->fetch());
        while ($row = $ps->fetch()) {
            $tableau[] = new Livre($row[0],$row[1],$row[2]);
        }
        # Pour debug : affichage du tableau à renvoyer
        # var_dump($tableau);
        return $tableau;
    }

    public function insert_livre($titre,$auteur) {
        # Solution d'INSERT avec prepared statement
        $query = 'INSERT INTO livres (titre, auteur) values (:titre,:auteur)';
        $ps = $this->_db->prepare($query);
        $ps->bindValue(':titre',$titre);
        $ps->bindValue(':auteur',$auteur);
        return $ps->execute();
    }
}
