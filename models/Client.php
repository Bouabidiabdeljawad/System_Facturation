    <?php

    class Client
    {
        private $id;
        private $typeClient;
        private $nom;
        private $prenom;
        private $adresse;
        private $ville; // Ajout de la propriété Ville
        private $codePostal; // Ajout de la propriété Code Postal
        private $email;
        private $telephone;
        private $dateInscription;
        private $dateNaissance;
        private $dateCreation;
        private $codeFiscal;

        public function __construct($bd)
        {
            $this->bd = $bd;
        }

        // Getters et Setters pour Ville
        public function getVille()
        {
            return $this->ville;
        }

        public function setVille($ville)
        {
            $this->ville = $ville;
        }

        // Getters et Setters pour Code Postal
        public function getCodePostal()
        {
            return $this->codePostal;
        }

        public function setCodePostal($codePostal)
        {
            $this->codePostal = $codePostal;
        }


        
        public function getId()
        {
            return $this->id;
        }

        public function setId($id)
        {
            $this->id = $id;
        }

        public function getTypeClient()
        {
            return $this->typeClient;
        }

        public function setTypeClient($typeClient)
        {
            $this->typeClient = $typeClient;
        }

        public function getNom()
        {
            return $this->nom;
        }

        public function setNom($nom)
        {
            $this->nom = $nom;
        }

        public function getPrenom()
        {
            return $this->prenom;
        }

        public function setPrenom($prenom)
        {
            $this->prenom = $prenom;
        }

        public function getAdresse()
        {
            return $this->adresse;
        }

        public function setAdresse($adresse)
        {
            $this->adresse = $adresse;
        }

        public function getEmail()
        {
            return $this->email;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function getTelephone()
        {
            return $this->telephone;
        }

        public function setTelephone($telephone)
        {
            $this->telephone = $telephone;
        }

        public function getDateInscription()
        {
            return $this->dateInscription;
        }

        public function setDateInscription($dateInscription)
        {
            $this->dateInscription = $dateInscription;
        }

        public function getDateNaissance()
        {
            return $this->dateNaissance;
        }

        public function setDateNaissance($dateNaissance)
        {
            $this->dateNaissance = $dateNaissance;
        }

        public function getDateCreation()
        {
            return $this->dateCreation;
        }

        public function setDateCreation($dateCreation)
        {
            $this->dateCreation = $dateCreation;
        }

        public function getCodeFiscal()
        {
            return $this->codeFiscal;
        }

        public function setCodeFiscal($codeFiscal)
        {
            $this->codeFiscal = $codeFiscal;
        }

    



        public function exists($field, $value)
        {
            $query = $this->bd->prepare("SELECT COUNT(*) FROM client WHERE $field = :value");
            $query->bindParam(':value', $value, PDO::PARAM_STR);
            $query->execute();
            return $query->fetchColumn() > 0;
        }
        public function obtenirTousLesClientsAvecAnniversaireAujourdhui()
        {
            $dateAujourdhui = date('Y-m-d');  // Format de la date: 'YYYY-MM-DD'
            $requete = $this->bd->prepare("
                SELECT * FROM client 
                WHERE DATE_FORMAT(date_naissance, '%m-%d') = DATE_FORMAT(:dateAujourdhui, '%m-%d')
            ");
            $requete->bindParam(':dateAujourdhui', $dateAujourdhui);
            $requete->execute();
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        }

        
        public function obtenirTousLesClients()
        {
            $requete = $this->bd->query("SELECT * FROM client");
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        }

        
        public function obtenirClientsParFiltres($type_client, $date_start, $date_end)
        {
            $query = "SELECT * FROM client WHERE 1=1";
            $params = [];

            if ($type_client !== 'tout') {
                $query .= " AND type_client = :type_client";
                $params['type_client'] = $type_client;
            }

            if (!empty($date_start)) {
                $query .= " AND date_inscription >= :date_start";
                $params['date_start'] = $date_start;
            }

            if (!empty($date_end)) {
                $query .= " AND date_inscription <= :date_end";
                $params['date_end'] = $date_end;
            }

            $requete = $this->bd->prepare($query);
            $requete->execute($params);

            return $requete->fetchAll(PDO::FETCH_ASSOC);
        }


    

        public function creerClient($type_client)
        {
            try {
                if ($this->typeClient == 'particulier') {
                    $requete = $this->bd->prepare("INSERT INTO client (type_client, nom, prenom, adresse, ville, code_postal, email, telephone, date_naissance, date_inscription)
                    VALUES (:type_client, :nom, :prenom, :adresse, :ville, :code_postal, :email, :telephone, :date_naissance, :date_inscription)");
        
                    $requete->execute([
                        'type_client' => $this->typeClient,
                        'nom' => $this->nom,
                        'prenom' => $this->prenom,
                        'adresse' => $this->adresse,
                        'ville' => $this->ville,
                        'code_postal' => $this->codePostal,
                        'email' => $this->email,
                        'telephone' => $this->telephone,
                        'date_naissance' => $this->dateNaissance,
                        'date_inscription' => $this->dateInscription
                    ]);
                } elseif ($this->typeClient == 'societe') {
                    $requete = $this->bd->prepare("INSERT INTO client (type_client, nom, adresse, ville, code_postal, email, telephone, date_inscription, date_creation, code_fiscal)
                    VALUES (:type_client, :nom, :adresse, :ville, :code_postal, :email, :telephone, :date_inscription, :date_creation, :code_fiscal)");
        
                    $requete->execute([
                        'type_client' => $this->typeClient,
                        'nom' => $this->nom,
                        'adresse' => $this->adresse,
                        'ville' => $this->ville,
                        'code_postal' => $this->codePostal,
                        'email' => $this->email,
                        'telephone' => $this->telephone,
                        'date_inscription' => $this->dateInscription,
                        'date_creation' => $this->dateCreation,
                        'code_fiscal' => $this->codeFiscal
                    ]);
                } else {
                    throw new Exception("Type de client inconnu");
                }
            } catch (PDOException $e) {
                throw new Exception("Erreur lors de la création du client : " . $e->getMessage());
            }
        }
        

        public function obtenirClientParId($id)
        {
            $requete = $this->bd->prepare("SELECT * FROM client WHERE id = :id");
            $requete->bindParam(':id', $id, PDO::PARAM_INT);
            $requete->execute();
            return $requete->fetch(PDO::FETCH_ASSOC);
        }



        public function rechercherClients($search_query, $type_client, $date_start, $date_end)
        {
            // Commencer la requête SQL
            $query = "SELECT * FROM client WHERE 1=1";
            $params = [];
        
            // Filtrer par type de client
            if ($type_client !== 'tout') {
                $query .= " AND type_client = :type_client";
                $params['type_client'] = $type_client;
            }
        
            // Filtrer par recherche combinée (nom, email, adresse)
            if (!empty($search_query)) {
                $query .= " AND (nom LIKE :search_query OR email LIKE :search_query OR adresse LIKE :search_query)";
                $params['search_query'] = '%' . $search_query . '%';  // Rechercher une correspondance partielle
            }
        
            // Filtrer par date de début d'inscription
            if (!empty($date_start)) {
                $query .= " AND date_inscription >= :date_start";
                $params['date_start'] = $date_start;
            }
        
            // Filtrer par date de fin d'inscription
            if (!empty($date_end)) {
                $query .= " AND date_inscription <= :date_end";
                $params['date_end'] = $date_end;
            }
        
            // Préparer et exécuter la requête
            $requete = $this->bd->prepare($query);
            $requete->execute($params);
        
            // Retourner les résultats
            return $requete->fetchAll(PDO::FETCH_ASSOC);
        }
        

        public function modifierClient($id)
        {
            $type_client = $_POST['type_client'];
        
            if ($type_client === 'societe') {
                $requete = $this->bd->prepare("UPDATE client 
                    SET 
                        type_client = :type_client,
                        nom = :nom, 
                        prenom = NULL, 
                        adresse = :adresse, 
                        ville = :ville, 
                        code_postal = :code_postal, 
                        email = :email, 
                        telephone = :telephone, 
                        date_naissance = NULL, 
                        date_inscription = :date_inscription, 
                        date_creation = :date_creation, 
                        code_fiscal = :code_fiscal 
                    WHERE id = :id");
        
                $requete->execute([
                    'type_client' => $this->typeClient,
                    'nom' => $this->nom,
                    'adresse' => $this->adresse,
                    'ville' => $this->ville,
                    'code_postal' => $this->codePostal,
                    'email' => $this->email,
                    'telephone' => $this->telephone,
                    'date_inscription' => $this->dateInscription,
                    'date_creation' => $this->dateCreation,
                    'code_fiscal' => $this->codeFiscal,
                    'id' => $id
                ]);
            } elseif ($type_client === 'particulier') {
                $requete = $this->bd->prepare("UPDATE client 
                    SET 
                        type_client = :type_client,
                        nom = :nom, 
                        prenom = :prenom, 
                        adresse = :adresse, 
                        ville = :ville, 
                        code_postal = :code_postal, 
                        email = :email, 
                        telephone = :telephone, 
                        date_naissance = :date_naissance, 
                        date_inscription = :date_inscription, 
                        date_creation = NULL, 
                        code_fiscal = NULL 
                    WHERE id = :id");
        
                $requete->execute([
                    'type_client' => $this->typeClient,
                    'nom' => $this->nom,
                    'prenom' => $this->prenom,
                    'adresse' => $this->adresse,
                    'ville' => $this->ville,
                    'code_postal' => $this->codePostal,
                    'email' => $this->email,
                    'telephone' => $this->telephone,
                    'date_naissance' => $this->dateNaissance,
                    'date_inscription' => $this->dateInscription,
                    'id' => $id
                ]);
            } else {
                throw new Exception("Type de client inconnu");
            }
        }
        
        
        public function supprimerClient($id)
        {
            $requete = $this->bd->prepare("DELETE FROM client WHERE id = :id");
            $requete->execute(['id' => $id]);
        }
    }
    ?>
