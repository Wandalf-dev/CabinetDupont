public function getUnavailableSlots() {
    // Vérifie si l'utilisateur est connecté et est un médecin
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'MEDECIN') {
        http_response_code(403);
        echo json_encode(['error' => 'Accès non autorisé']);
        return;
    }

    // Récupère les dates de début et de fin depuis la requête
    $dateDebut = $_GET['start'] ?? date('Y-m-d');
    $dateFin = $_GET['end'] ?? date('Y-m-d', strtotime('+7 days'));

    // Récupère l'agenda du médecin
    $agenda = $this->agendaModel->getAgendaByUtilisateur($_SESSION['user_id']);
    if (!$agenda) {
        http_response_code(404);
        echo json_encode(['error' => 'Agenda non trouvé']);
        return;
    }

    // Récupère les créneaux indisponibles
    $creneauModel = new \App\Models\CreneauModel();
    $creneauxIndisponibles = $creneauModel->getCreneauxIndisponiblesByPeriod($dateDebut, $dateFin, $agenda['id']);

    // Formater les créneaux pour le JavaScript
    $formattedUnavailable = [];
    foreach ($creneauxIndisponibles as $creneau) {
        $formattedUnavailable[] = [
            'id' => $creneau['id'],
            'start' => $creneau['debut'],
            'end' => $creneau['fin']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($formattedUnavailable);
}