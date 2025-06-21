<?php

declare(strict_types=1);

namespace Dive\Controller;

use Dive\Repository\WebUserRepository;
use Dive\Model\WebUser;
use PDO;
use DI\Attribute\Inject;


class WebUsersController extends AbstractController
{
    #[Inject]
    private PDO $dbo;

    #[Inject]
    private WebUserRepository $webUserRepository;

    /**
     * Aufbau der Standardseite. Suche und Auflistung der der Benutzer
     */
    public function list(): void
    {
        echo $this->render('WebUsers/index.html.twig');
    }

    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $mail = $_POST['mail'];

            $newUser = new WebUser($username, $password, $mail);
            
            if ($this->webUserRepository->create($newUser)) {
                $_SESSION['message']="User erfolgreich erstellt!";
            } else {
                $_SESSION['message']="Fehler beim Erstellen des Users.";
            }
        } else {
            echo $this->render('Users/add.html.twig');
        }
    }

    public function fetch(): void
    {
        //TODO get Data from the Repository
        header('Content-Type: application/json');

        // Parameter von DataTables
        $limit = $_POST['length']; // Anzahl der Datensätze pro Seite
        $offset = $_POST['start']; // Startindex
        $search = $_POST['search']['value']; // Suchbegriff

        // Gesamtanzahl der Datensätze in der Datenbank
        // Muss ich das Repository injecten? es hat doch nur standardwerte?
        $totalRecords = $this->webUserRepository->getTotalRecords();

        // Suchabfrage
        $sql = "SELECT id, mail FROM users";
        if (!empty($search)) {
            $sql .= " WHERE mail LIKE :search";
        }
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->dbo->prepare($sql);
        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gesamtanzahl der gefilterten Datensätze
        $totalFiltered = empty($search) ? $totalRecords : $stmt->rowCount();

        echo json_encode([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $users
        ]);
    }
}
