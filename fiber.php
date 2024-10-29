<?php

declare(strict_types=1);


// Fonction qui simule un traitement long
function longRunningTask(string $taskName): Generator
{
    echo "Démarrage de $taskName\n";

    // Simule un premier traitement
    echo "$taskName : Étape 1 en cours...\n";
    yield;  // Permet à d'autres tâches de s'exécuter

    // Simule un deuxième traitement
    echo "$taskName : Étape 2 en cours...\n";
    yield;

    // Simule un troisième traitement
    echo "$taskName : Étape 3 en cours...\n";
    yield;

    echo "Fin de $taskName\n";
    return "Résultat de $taskName";
}

// Fonction qui exécute une tâche dans une Fiber
function runTaskInFiber(string $taskName): Fiber
{
    return new Fiber(function () use ($taskName) {
        $generator = longRunningTask($taskName);

        while ($generator->valid()) {
            $generator->next();
            Fiber::suspend();  // Suspend l'exécution et permet à d'autres Fibers de s'exécuter
        }

        return $generator->getReturn();
    });
}

// Création des Fibers pour différentes tâches
$fiber1 = runTaskInFiber("Tâche A");
$fiber2 = runTaskInFiber("Tâche B");
$fiber3 = runTaskInFiber("Tâche C");

// Tableau pour stocker les Fibers actives
$activeFibers = [$fiber1, $fiber2, $fiber3];

// Exécution entrelacée des Fibers
while (!empty($activeFibers)) {
    foreach ($activeFibers as $index => $fiber) {
        if (!$fiber->isStarted()) {
            $fiber->start();
        } elseif (!$fiber->isTerminated()) {
            $fiber->resume();
        } else {
            // La Fiber est terminée, on récupère son résultat
            $result = $fiber->getReturn();
            echo "Résultat reçu : $result\n";
            unset($activeFibers[$index]);
        }
    }
}

echo "Toutes les tâches sont terminées\n";