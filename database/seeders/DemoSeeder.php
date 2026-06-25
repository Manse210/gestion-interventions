<?php

namespace Database\Seeders;

use App\Models\AppNotification;
use App\Models\Evaluation;
use App\Models\Report;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    private function d(int $daysAgo): string
    {
        return Carbon::now()->subDays($daysAgo)->toDateTimeString();
    }

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Nadia Kourouma',
            'email' => 'nadia.k@atech-cyber.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        $tech1 = User::create([
            'name' => 'Amadou Diarra',
            'email' => 'a.diarra@atech-cyber.com',
            'password' => Hash::make('tech123'),
            'role' => 'technicien',
            'specialite' => 'Réseau',
        ]);

        $tech2 = User::create([
            'name' => 'Sofia Benamor',
            'email' => 's.benamor@atech-cyber.com',
            'password' => Hash::make('tech123'),
            'role' => 'technicien',
            'specialite' => 'Matériel',
        ]);

        $tech3 = User::create([
            'name' => 'Karim El Fassi',
            'email' => 'k.elfassi@atech-cyber.com',
            'password' => Hash::make('tech123'),
            'role' => 'technicien',
            'specialite' => 'Logiciel',
        ]);

        $client1 = User::create([
            'name' => 'Léa Morel',
            'email' => 'lea.morel@client.fr',
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        $client2 = User::create([
            'name' => 'IT Norda',
            'email' => 'it@norda.io',
            'password' => Hash::make('client123'),
            'role' => 'client',
        ]);

        $tickets = [
            ['ref'=>'TK-001','titre'=>'Pare-feu principal — Règles de filtrage','description'=>'Règles obsolètes, flux suspects non bloqués.','priorite'=>'Critique','categorie'=>'Réseau','statut'=>'En cours','client_id'=>5,'technicien_id'=>2,'created_at'=>$this->d(2),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-002','titre'=>'Station compta HS – démarrage impossible','description'=>'PC-DK-44, échec POST code 0x55.','priorite'=>'Haute','categorie'=>'Matériel','statut'=>'Ouvert','client_id'=>6,'technicien_id'=>3,'created_at'=>$this->d(1),'updated_at'=>$this->d(1)],
            ['ref'=>'TK-003','titre'=>'Migration EDR bloquée – conflit signature','description'=>'Agent SentinelOne ne démarre pas après patch.','priorite'=>'Haute','categorie'=>'Logiciel','statut'=>'Résolu','client_id'=>5,'technicien_id'=>4,'created_at'=>$this->d(6),'updated_at'=>$this->d(1)],
            ['ref'=>'TK-004','titre'=>'Switch bâtiment B - boucle STP','description'=>'Broadcast storm détecté, ports 12-18 flapping.','priorite'=>'Critique','categorie'=>'Réseau','statut'=>'En cours','client_id'=>6,'technicien_id'=>2,'created_at'=>$this->d(3),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-005','titre'=>'UPS salle serveur – batterie faible','description'=>'APC SRT 6kVA, alarme batterie module #2.','priorite'=>'Moyenne','categorie'=>'Électrique','statut'=>'Ouvert','client_id'=>5,'technicien_id'=>null,'created_at'=>$this->d(5),'updated_at'=>$this->d(5)],
            ['ref'=>'TK-006','titre'=>'Déploiement MFA Microsoft 365','description'=>'Basculer 110 comptes sur FIDO2.','priorite'=>'Moyenne','categorie'=>'Logiciel','statut'=>'Fermé','client_id'=>6,'technicien_id'=>4,'created_at'=>$this->d(18),'updated_at'=>$this->d(7)],
            ['ref'=>'TK-007','titre'=>'NAS backup corrompu – volume degraded','description'=>'RAID5 QNAP TS-832X - 1 disque failed.','priorite'=>'Haute','categorie'=>'Matériel','statut'=>'En cours','client_id'=>5,'technicien_id'=>3,'created_at'=>$this->d(1),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-008','titre'=>'Wi-Fi invité isolé – VLAN Guest non routé','description'=>'SSIDs invités ne sortent plus, DHCP OK.','priorite'=>'Basse','categorie'=>'Réseau','statut'=>'Résolu','client_id'=>6,'technicien_id'=>2,'created_at'=>$this->d(11),'updated_at'=>$this->d(4)],
            ['ref'=>'TK-009','titre'=>'Poste CAO lent – SSD throttling','description'=>'WS-HP-Z4, temps disque >82°C.','priorite'=>'Moyenne','categorie'=>'Matériel','statut'=>'Ouvert','client_id'=>5,'technicien_id'=>3,'created_at'=>$this->d(0),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-010','titre'=>'SIRH – erreur 500 export paie','description'=>'Module paie plante export SEPA Q1.','priorite'=>'Haute','categorie'=>'Logiciel','statut'=>'Ouvert','client_id'=>6,'technicien_id'=>null,'created_at'=>$this->d(0),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-011','titre'=>'Migration DC – AD sync','description'=>'Sync AD ralentie après migration contrôleur.','priorite'=>'Haute','categorie'=>'Réseau','statut'=>'En cours','client_id'=>5,'technicien_id'=>2,'created_at'=>$this->d(4),'updated_at'=>$this->d(1)],
            ['ref'=>'TK-012','titre'=>'Alarme incendie – test capteurs','description'=>'Maintenance trimestrielle capteurs B1-B4.','priorite'=>'Moyenne','categorie'=>'Électrique','statut'=>'Ouvert','client_id'=>6,'technicien_id'=>null,'created_at'=>$this->d(6),'updated_at'=>$this->d(6)],
            ['ref'=>'TK-013','titre'=>'VPN nomade – latence critique','description'=>'Temps réponse >800ms depuis 48h.','priorite'=>'Critique','categorie'=>'Réseau','statut'=>'Résolu','client_id'=>5,'technicien_id'=>2,'created_at'=>$this->d(14),'updated_at'=>$this->d(9)],
            ['ref'=>'TK-014','titre'=>'Phishing ciblé – compromission boîte','description'=>'Alerte SOC: phishing creds CFO détecté.','priorite'=>'Critique','categorie'=>'Logiciel','statut'=>'En cours','client_id'=>6,'technicien_id'=>4,'created_at'=>$this->d(1),'updated_at'=>$this->d(0)],
            ['ref'=>'TK-015','titre'=>'Scanner réseau – panne mécanique','description'=>'Bac défectueux, code erreur E-12.','priorite'=>'Basse','categorie'=>'Matériel','statut'=>'Ouvert','client_id'=>5,'technicien_id'=>3,'created_at'=>$this->d(3),'updated_at'=>$this->d(3)],
        ];

        foreach ($tickets as $data) {
            Ticket::create($data);
        }

        // History
        TicketHistory::create(['ticket_id'=>1,'user_id'=>5,'action'=>'Ticket créé','created_at'=>$this->d(2),'updated_at'=>$this->d(2)]);
        TicketHistory::create(['ticket_id'=>1,'user_id'=>1,'action'=>'Assigné à Amadou Diarra','created_at'=>$this->d(2),'updated_at'=>$this->d(2)]);
        TicketHistory::create(['ticket_id'=>1,'user_id'=>2,'action'=>'Statut → En cours','created_at'=>$this->d(0),'updated_at'=>$this->d(0)]);
        TicketHistory::create(['ticket_id'=>3,'user_id'=>5,'action'=>'Ticket créé','created_at'=>$this->d(6),'updated_at'=>$this->d(6)]);
        TicketHistory::create(['ticket_id'=>3,'user_id'=>1,'action'=>'Assigné à Karim El Fassi','created_at'=>$this->d(6),'updated_at'=>$this->d(6)]);
        TicketHistory::create(['ticket_id'=>3,'user_id'=>4,'action'=>'Statut → Résolu • Rapport soumis','created_at'=>$this->d(1),'updated_at'=>$this->d(1)]);
        TicketHistory::create(['ticket_id'=>4,'user_id'=>6,'action'=>'Ticket créé','created_at'=>$this->d(3),'updated_at'=>$this->d(3)]);
        TicketHistory::create(['ticket_id'=>4,'user_id'=>1,'action'=>'Assigné à Amadou Diarra','created_at'=>$this->d(3),'updated_at'=>$this->d(3)]);
        TicketHistory::create(['ticket_id'=>4,'user_id'=>2,'action'=>'Statut → En cours','created_at'=>$this->d(0),'updated_at'=>$this->d(0)]);
        TicketHistory::create(['ticket_id'=>6,'user_id'=>6,'action'=>'Ticket créé','created_at'=>$this->d(18),'updated_at'=>$this->d(18)]);
        TicketHistory::create(['ticket_id'=>6,'user_id'=>4,'action'=>'Rapport soumis • Résolu','created_at'=>$this->d(8),'updated_at'=>$this->d(8)]);
        TicketHistory::create(['ticket_id'=>6,'user_id'=>6,'action'=>'Évalué 5★ • Fermé','created_at'=>$this->d(7),'updated_at'=>$this->d(7)]);
        TicketHistory::create(['ticket_id'=>8,'user_id'=>6,'action'=>'Ticket créé','created_at'=>$this->d(11),'updated_at'=>$this->d(11)]);
        TicketHistory::create(['ticket_id'=>8,'user_id'=>2,'action'=>'Rapport soumis • Résolu','created_at'=>$this->d(4),'updated_at'=>$this->d(4)]);

        // Comments
        TicketComment::create(['ticket_id'=>1,'user_id'=>2,'text'=>'Isolation en cours, règle NAT corrompue suspectée.','internal'=>false,'created_at'=>$this->d(1),'updated_at'=>$this->d(1)]);
        TicketComment::create(['ticket_id'=>1,'user_id'=>2,'text'=>'Contacter équipe SOC pour corrélation EDR.','internal'=>true,'created_at'=>$this->d(0),'updated_at'=>$this->d(0)]);

        // Reports
        Report::create(['ticket_id'=>3,'travaux'=>"Analyse conflit KB / driver SentinelOne. Rollback ciblé sur 42 postes. Exception driver signée. Redéploiement progressif EDR 24.3 avec validation SIEM.",'duree_heures'=>5,'duree_minutes'=>40,'materiel'=>'Aucun. Script PS + GPO.','observations'=>'Driver tiers non signé. Doc ajoutée au runbook.','recommandations'=>'Politique WHQL. Canary group avant patch mardi.','signed_by'=>'Karim El Fassi','created_at'=>$this->d(1),'updated_at'=>$this->d(1)]);
        Report::create(['ticket_id'=>6,'travaux'=>"Déploiement MFA 365 – 110 utilisateurs migrés vers FIDO2. CA policies: blocage legacy auth, geo-filtering, risk-based.",'duree_heures'=>18,'duree_minutes'=>10,'materiel'=>'32 clés YubiKey 5 NFC.','observations'=>'3 comptes service en exception, documentés.','recommandations'=>'Passwordless étendu. CA re-auth 48h pour rôles sensibles.','signed_by'=>'Karim El Fassi','created_at'=>$this->d(8),'updated_at'=>$this->d(8)]);
        Report::create(['ticket_id'=>8,'travaux'=>"Correction route VLAN 240 Guest. ACL firewall réalignée. Rétablissement NAT sortant. Tests captifs OK.",'duree_heures'=>1,'duree_minutes'=>55,'materiel'=>'-','observations'=>'Policy mal fusionnée lors d\'un changement antérieur.','recommandations'=>'Change-tracking FortiManager. Audit trimestriel ACL Guest.','signed_by'=>'Amadou Diarra','created_at'=>$this->d(4),'updated_at'=>$this->d(4)]);

        // Evaluations
        Evaluation::create(['ticket_id'=>6,'client_id'=>6,'note'=>5,'commentaire'=>'Intervention très propre, documentation parfaite.','created_at'=>$this->d(7),'updated_at'=>$this->d(7)]);
        Evaluation::create(['ticket_id'=>8,'client_id'=>6,'note'=>4,'commentaire'=>'Rapide et efficace. Communication à améliorer.','created_at'=>$this->d(3),'updated_at'=>$this->d(3)]);

        // Notifications
        AppNotification::create(['user_id'=>2,'title'=>'SLA critique approchant','message'=>'TK-004 • Switch bâtiment B – échéance imminente','ticket_ref'=>'TK-004']);
        AppNotification::create(['user_id'=>5,'title'=>'Rapport disponible','message'=>'TK-003 résolu – rapport consultable','ticket_ref'=>'TK-003']);
        AppNotification::create(['user_id'=>1,'title'=>'Ticket non assigné','message'=>'TK-010 (Logiciel – Haute) en attente d\'assignation','ticket_ref'=>'TK-010']);
        AppNotification::create(['user_id'=>1,'title'=>'Évaluation reçue','message'=>'Karim El Fassi • 5/5 sur TK-006','ticket_ref'=>'TK-006']);
        AppNotification::create(['user_id'=>4,'title'=>'Nouveau ticket','message'=>'TK-014 – Phishing ciblé – priorité Critique','ticket_ref'=>'TK-014']);
    }
}
