<?php

namespace Database\Seeders;

use App\Models\Arrondissement;
use App\Models\Commune;
use App\Models\Departement;
use App\Models\Pay;
use App\Models\Quartier;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    private array $countries = [
        ['nom' => 'Burkina Faso', 'code' => 'BF', 'indicatif' => '+226'],
        ['nom' => 'Bénin', 'code' => 'BJ', 'indicatif' => '+229'],
        ['nom' => "Côte d'Ivoire", 'code' => 'CI', 'indicatif' => '+225'],
        ['nom' => 'Mali', 'code' => 'ML', 'indicatif' => '+223'],
        ['nom' => 'Niger', 'code' => 'NE', 'indicatif' => '+227'],
        ['nom' => 'Sénégal', 'code' => 'SN', 'indicatif' => '+221'],
        ['nom' => 'Togo', 'code' => 'TG', 'indicatif' => '+228'],
    ];

    private array $geo = [
        'Burkina Faso' => [
            'departements' => [
                ['nom' => 'Bam', 'communes' => ['Kongoussi', 'Bourzanga', 'Rollo', 'Tikaré', 'Zimtenga', 'Nasséré', 'Sabou']],
                ['nom' => 'Bazèga', 'communes' => ['Kombissiri', 'Doulougou', 'Kayao', 'Toécé', 'Gaongo', 'Ipelcégré', 'Saponé']],
                ['nom' => 'Bougouriba', 'communes' => ['Diébougou', 'Bondigui', 'Iolonioro', 'Tiankoura', 'Dolo', 'Kpéridiéga', 'Niogo']],
                ['nom' => 'Boulgou', 'communes' => ['Tenkodogo', 'Bittou', 'Bané', 'Béguédo', 'Boussouma', 'Garango', 'Komtoèga', 'Niaogho', 'Zonsé', 'Zoaga', 'Ouargaye', 'Sangha', 'Zabré', 'Yargatenga']],
                ['nom' => 'Boulkiemdé', 'communes' => ['Koudougou', 'Kindi', 'Nanoro', 'Bingo', 'Imasgo', 'Ramongo', 'Siglé', 'Thyou', 'Soa', 'Poé', 'Pella', 'Sourgou', 'Sigli', 'Nandiala', 'Sabou', 'Kayao-N', 'Yako']],
                ['nom' => 'Comoé', 'communes' => ['Banfora', 'Mangodara', 'Sidéradougou', 'Niangoloko', 'Ouo', 'Tiéfora', 'Soubakaniédougou', 'Kourinion', 'Diaraba', 'Léraba', 'Koloko', 'Sindo', 'Noumoudara']],
                ['nom' => 'Ganzourgou', 'communes' => ['Méguet', 'Mogtédo', 'Boudry', 'Zorgho', 'Salogo', 'Zam', 'Zoungou', 'Kogho', 'Donsin', 'Bissiga', 'Cissé']],
                ['nom' => 'Gnagna', 'communes' => ['Bogandé', 'Coalla', 'Manni', 'Piélabougou', 'Thion', 'Vallée du Niger', 'Liptougou', 'Komondjari', 'Tankoa', 'Matiakoali', 'Kodaga', 'Gorgadji', 'Sambla', 'Soudougui', 'Bartiébougou']],
                ['nom' => 'Gourma', 'communes' => ['Fada N\'Gourma', 'Diapaga', 'Matéri', 'Pama', 'Tansarga', 'Yamba', 'Boungou', 'Logobou', 'Namtoungou', 'Partiaga', 'Piéla', 'Matiékoa', 'Kompienga', 'Kompiembiga', 'Motiabouli']],
                ['nom' => 'Houet', 'communes' => ['Bobo-Dioulasso', 'Bama', 'Banzon', 'Béréba', 'Bounou', 'Dandé', 'Faramana', 'Fô', 'Karangasso-Vigué', 'Karangasso-Sambla', 'Koumi', 'Koumbia', 'Léna', 'Makognè', 'Padéma', 'Péni', 'Poura', 'Satiri', 'Sami', 'Séo', 'Toussiana']],
                ['nom' => 'Ioba', 'communes' => ['Dano', 'Dissin', 'Guéguéré', 'Koper', 'Niégo', 'Oronkua', 'Ouéssa', 'Zamou', 'Kpéridiéga', 'Kankalaba', 'Goguin', 'Boromo', 'Safané', 'Yaho', 'Firka', 'Bottié']],
                ['nom' => 'Kadiogo', 'communes' => ['Ouagadougou', 'Komsilga', 'Tanghin-Dassouri', 'Pabré', 'Saaba', 'Koubri', 'Laye', 'Gourcy', 'Barsalogho', 'Nagréongo', 'Pô', 'Boudry', 'Kokologho', 'Sapouy', 'Bingo', 'Sourgou', 'Saponé', 'Ouèguèssa', 'Dapélogo', 'Toéssin', 'Boussé', 'Niou', 'Siglé']],
                ['nom' => 'Kénédougou', 'communes' => ['Orodara', 'Bougouriba-N', 'Kankalaba', 'Mondon', 'Morolaba', 'Samorogouan', 'Samou', 'Sindou', 'Kourinion', 'Diaraba', 'Léraba', 'Koloko', 'Sindo', 'Noumoudara']],
                ['nom' => 'Komondjari', 'communes' => ['Gayeri', 'Bartiebougou', 'Foutouri', 'Kompiembiga', 'Namtoungou', 'Piela', 'Tansarga', 'Yamba']],
                ['nom' => 'Kompienga', 'communes' => ['Pama', 'Kompienga', 'Matiakouali', 'Nagtenga', 'Tansobentenga', 'Yondé', 'Zèssa', 'Bonam', 'Lankoué']],
                ['nom' => 'Kossi', 'communes' => ['Nouma', 'Bomborokuy', 'Dégué', 'Djibasso', 'Fafo', 'Kombori', 'Madouba', 'Mènè', 'Mokodougou', 'Sono', 'Barani', 'Soumbou', 'Sanga', 'Bourasso', 'Dokui']],
                ['nom' => 'Koulpélogo', 'communes' => ['Ouargaye', 'Comin-Yanga', 'Dourtenga', 'Lalgué', 'Sanga', 'Soudougui', 'Yargatenga', 'Zamsé', 'Zèko', 'Zoaga']],
                ['nom' => 'Kourwéogo', 'communes' => ['Boussé', 'Laye', 'Niou', 'Sourgou', 'Toéghin', 'Nartenga', 'Kougri', 'Sambo', 'Base', 'Bingo', 'Basgou', 'Lingogo', 'Villers', 'Fournaya', 'Nobéré', 'Saponé']],
                ['nom' => 'Léraba', 'communes' => ['Sindou', 'Dakoro', 'Douna', 'Kankalaba', 'Niankorodougou', 'Ouara', 'Samorogouan', 'Soubakaniédougou', 'Noumoudara', 'Mondon']],
                ['nom' => 'Loroum', 'communes' => ['Titao', 'Banh', 'Dollo', 'Sollé', 'Ouindigui', 'Barga', 'Koumbri', 'Sona', 'Yako']],
                ['nom' => 'Mouhoun', 'communes' => ['Dédougou', 'Bondoukuy', 'Boromo', 'Ouarkoye', 'Safané', 'Tcheriba', 'Yaho', 'Douroula', 'Sana', 'Ouona', 'Dokuy', 'Kona', 'Kassoum', 'Kombori', 'Madouba']],
            ],
        ],
        'Bénin' => [
            'departements' => [
                ['nom' => 'Alibori', 'communes' => ['Kandi', 'Banikoara', 'Gogounou', 'Karimama', 'Malanville', 'Ségbana']],
                ['nom' => 'Atacora', 'communes' => ['Natitingou', 'Boukoumbé', 'Cobly', 'Kérou', 'Kouandé', 'Matéri', 'Pehunco', 'Tanguiéta', 'Toucountouna']],
                ['nom' => 'Atlantique', 'communes' => ['Ouidah', 'Abomey-Calavi', 'Allada', 'Kpomassè', 'Sô-Ava', 'Toffo', 'Tori-Bossito', 'Zè']],
                ['nom' => 'Borgou', 'communes' => ['Parakou', 'Bembéréké', 'Kalalé', 'N\'Dali', 'Nikki', 'Pèrèrè', 'Sinendé', 'Tchaourou']],
                ['nom' => 'Collines', 'communes' => ['Dassa-Zoumé', 'Bantè', 'Glazoué', 'Ouèssè', 'Savalou', 'Savé']],
                ['nom' => 'Couffo', 'communes' => ['Aplahoué', 'Djakotomey', 'Dogbo', 'Klouékanmè', 'Lalo', 'Toviklin']],
                ['nom' => 'Donga', 'communes' => ['Djougou', 'Bassila', 'Copargo', 'Ouaké']],
                ['nom' => 'Littoral', 'communes' => ['Cotonou']],
                ['nom' => 'Mono', 'communes' => ['Lokossa', 'Athiémé', 'Bopa', 'Comè', 'Grand-Popo', 'Houéyogbé']],
                ['nom' => 'Ouémé', 'communes' => ['Porto-Novo', 'Adjarra', 'Adjohoun', 'Aguégués', 'Akpro-Missérété', 'Avrankou', 'Bonou', 'Dangbo', 'Sèmè-Kpodji']],
                ['nom' => 'Plateau', 'communes' => ['Sakété', 'Adja-Ouèrè', 'Ifangni', 'Kétou', 'Pobè']],
                ['nom' => 'Zou', 'communes' => ['Abomey', 'Agbangnizoun', 'Bohicon', 'Cové', 'Djidja', 'Ouinhi', 'Zagnanado', 'Za-Kpota', 'Zogbodomey']],
            ],
        ],
        "Côte d'Ivoire" => [
            'departements' => [
                ['nom' => 'Abidjan', 'communes' => ['Abidjan', 'Anyama', 'Cocody', 'Marcory', 'Plateau', 'Treichville', 'Yopougon', 'Koumassi', 'Port-Bouët', 'Adjamé', 'Attécoubé']],
                ['nom' => 'Bas-Sassandra', 'communes' => ['San-Pédro', 'Sassandra', 'Grand-Béréby', 'Tabou', 'Fresco', 'Guitry', 'Lakota', 'Divo']],
                ['nom' => 'Comoé', 'communes' => ['Abengourou', 'Agnibilékrou', 'Bettié', 'Tiapoum', 'Assinie', 'Adiaké', 'Aboisso', 'Grand-Bassam', 'Bonoua']],
                ['nom' => 'Denguélé', 'communes' => ['Odienné', 'Madinani', 'Samatiguila', 'Kaniasso', 'Minignan', 'Boundiali', 'Tengréla', 'Ferkessédougou']],
                ['nom' => 'Gôh-Djiboua', 'communes' => ['Gagnoa', 'Oumé', 'Divo', 'Lakota', 'Guéyo', 'Zikisso', 'Hiré', 'Ouragahio']],
                ['nom' => 'Lacs', 'communes' => ['Yamoussoukro', 'Toumodi', 'Tiébissou', 'Dimbokro', 'Bocanda', 'M\'Bahiakro', 'Didiévi', 'Kouassi-Kouassikro', 'Arrah', 'Bongouanou', 'Daoukro']],
                ['nom' => 'Lagunes', 'communes' => ['Dabou', 'Grand-Lahou', 'Jacqueville', 'Tiassalé', 'Sikensi', 'Attiécoubé', 'Songon', 'N\'Douci', 'Agboville', 'Céchi', 'Rubino', 'Akébéfou', 'M\'Brou']],
                ['nom' => 'Montagnes', 'communes' => ['Man', 'Danané', 'Biankouma', 'Zouan-Hounien', 'Touba', 'Sipilou', 'Gbonné', 'Logoualé', 'Facobly', 'Kouibli']],
                ['nom' => 'Sassandra-Marahoué', 'communes' => ['Daloa', 'Issia', 'Vavoua', 'Zoukougbeu', 'Bouaflé', 'Sinfra', 'Kononfla', 'Béoumi', 'Botro', 'Bouaké', 'Sakassou']],
                ['nom' => 'Savanes', 'communes' => ['Korhogo', 'Boundiali', 'Ferkessédougou', 'Tengréla', 'Dikodougou', 'Sinématiali', 'Niofoin', 'Kouto', 'Ouangolodougou', 'Poni', 'Gbéléban', 'Kasséré']],
                ['nom' => 'Vallée du Bandama', 'communes' => ['Bouaké', 'Beoumi', 'Botro', 'Sakassou', 'Dabakala', 'Katiola', 'Niakaramandougou', 'Tafiré', 'Djébonoua', 'Bodokro', 'Tortiya']],
                ['nom' => 'Woroba', 'communes' => ['Séguéla', 'Kani', 'Mankono', 'Kounahiri', 'Vavoua', 'Diawala', 'Kouassi', 'Sifié', 'Bloléquin', 'Diégonéfla', 'Gohitafla', 'Béoué', 'Touba']],
                ['nom' => 'Yamoussoukro', 'communes' => ['Yamoussoukro', 'Attiégouakro', 'Didievi', 'Tiébissou', 'Toumodi']],
                ['nom' => 'Zanzan', 'communes' => ['Bondoukou', 'Bouna', 'Tanda', 'Sandégué', 'Transua', 'Koun-Fao', 'Sokoro', 'Tambi', 'Goudou', 'Sominassé', 'N\'Gandana', 'Tapégué']],
            ],
        ],
        'Mali' => [
            'departements' => [
                ['nom' => 'Bamako', 'communes' => ['Bamako', 'Bagaré', 'Goumbou', 'Kati', 'Kalabancoro', 'Kambila', 'Mancourou', 'N\'Gouraba', 'Siby', 'Samaya', 'Mountougoula', 'Béléko', 'Dombila', 'Koumantou', 'Sido', 'Dialakorodji', 'Sirakoro']],
                ['nom' => 'Gao', 'communes' => ['Gao', 'Ansongo', 'Bourem', 'Ménaka', 'Tinfatout', 'N\'Tillit', 'Ouatougou', 'Labbezanga', 'Bamba', 'Sarmiougou', 'Boureïma', 'Haoussa', 'N\'Kouma', 'Tilemsi']],
                ['nom' => 'Kayes', 'communes' => ['Kayes', 'Bafoulabé', 'Diéma', 'Kéniéba', 'Kita', 'Nioro', 'Yélimané', 'Falémé', 'Gory', 'Sadiola', 'Séféto', 'Khossanto', 'Marena', 'Diboli', 'Sagalo', 'Bangassi', 'Toukoto', 'Somankidi']],
                ['nom' => 'Kidal', 'communes' => ['Kidal', 'Abeïbara', 'Aguelhok', 'Essouk', 'Timtaghène', 'Takoulotte', 'Boghassa', 'Tin-Essako', 'Adielhoc', 'Anéfis', 'Inekar', 'Tessalit', 'Tadmekka', 'Khalil']],
                ['nom' => 'Koulikoro', 'communes' => ['Koulikoro', 'Banamba', 'Dioïla', 'Kangaba', 'Kati', 'Kolokani', 'Nara', 'Négéla', 'Siby', 'Mande', 'Ouelessebougou', 'Faladié', 'Tiorola', 'Tourela', 'Dogodouman', 'Sébékoro', 'Béléko', 'Kona', 'Massantola', 'Nyamina']],
                ['nom' => 'Mopti', 'communes' => ['Mopti', 'Bandiagara', 'Bankass', 'Djenné', 'Douentza', 'Koro', 'Niafunké', 'Ténenkou', 'Youwarou', 'Sofara', 'Fombori', 'Kona', 'Ouenkoro', 'Dé', 'Kendé', 'Bembéré', 'Bérégou', 'Diamarba', 'Sassari', 'Pondori', 'N\'Gassabou', 'Farimaké', 'Diongaga']],
                ['nom' => 'Ségou', 'communes' => ['Ségou', 'Barouéli', 'Bla', 'Macina', 'Niono', 'San', 'Tominian', 'Markala', 'Dioro', 'Sansanding', 'Konodimini', 'Fatiné', 'Souba', 'Ké-Macina', 'Mandiakuy', 'Boidié', 'Kouro', 'Toga', 'Dabala', 'N\'Gassé', 'Koula']],
                ['nom' => 'Sikasso', 'communes' => ['Sikasso', 'Bougouni', 'Kadiolo', 'Kolondiéba', 'Koutiala', 'Yanfolila', 'Yorosso', 'Bélédougou', 'N\'Golo', 'Garalo', 'Koumantou', 'Tiongui', 'Djallon', 'N\'Bogoté', 'Noussimana', 'Bohoba', 'Sibirila', 'Finkolo', 'Kéléya', 'Kouoro', 'Ourikéla', 'Tao', 'Zanina', 'Zantiébougou']],
                ['nom' => 'Tombouctou', 'communes' => ['Tombouctou', 'Diré', 'Goundam', 'Gourma-Rharous', 'Niafunké', 'Araouane', 'Ber', 'Bourem-Inaly', 'Douékiré', 'Issa-Ber', 'Kanèye', 'Koumaïra', 'Léré', 'Salam', 'Saréyéré', 'Tinguéréguiff', 'Tonka', 'Dangha', 'Ondougou', 'Djaptodji', 'Haïbongo', 'Kondi', 'Madiakoye', 'Taykiri', 'Bintagoungou', 'Binguéoudou', 'Adarmalane', 'Déd M\'Bé', 'Sabougou', 'Sah', 'Daka-Occidental', 'Daka-Oriental', 'Bambara-Maoudé', 'N\'Kouma', 'Ouinerden', 'Télété', 'Tindirma', 'Tinghère', 'Zoumala']],
            ],
        ],
        'Niger' => [
            'departements' => [
                ['nom' => 'Agadez', 'communes' => ['Agadez', 'Arlit', 'Bilma', 'Iferouane', 'Tchirozérine', 'Dabaga', 'Timia', 'Ingall', 'Tabelot', 'Goubé', 'Tagazal', 'Djado', 'Fachi', 'Termit', 'Ténéré']],
                ['nom' => 'Diffa', 'communes' => ['Diffa', 'Bosso', 'Maïné-Soroa', 'N\'Guigmi', 'Kablewa', 'Chétimari', 'Kindjandi', 'Goudoumaria', 'Kélé', 'Koublé', 'Toumour', 'N\'Gourti', 'N\'Guélbèy', 'Karatou', 'Boudouri', 'Barwa', 'Kouka']],
                ['nom' => 'Dosso', 'communes' => ['Dosso', 'Boboye', 'Dioundiou', 'Dogondoutchi', 'Gaya', 'Loga', 'Tibiri', 'Falmèy', 'Fakara', 'Mokko', 'Sambéra', 'Tanda', 'Tombo', 'Yéni', 'Kiota', 'Koygolo', 'N\'Gonga', 'Sébéri', 'Dantchandou', 'Bargougou', 'Karakara', 'Bana', 'Kargouna', 'Soucoucoutane', 'Tounouga', 'Zabori']],
                ['nom' => 'Maradi', 'communes' => ['Maradi', 'Dakoro', 'Gazaoua', 'Guidan-Roumdji', 'Madarounfa', 'Mayahi', 'Tessaoua', 'Aguié', 'Bermo', 'Djirataoua', 'Gabi', 'Gangara', 'Goulbi', 'Guidan Sori', 'Hawandawaki', 'Issa Ouanni', 'Kanabak', 'Kornaka', 'Maiyara', 'Médé', 'N\'Gouan', 'Ourafane', 'Safo', 'Sarkin Yamma', 'Tagriss', 'Tibiri', 'Yachika', 'Zinder']],
                ['nom' => 'Tahoua', 'communes' => ['Tahoua', 'Abalak', 'Bagagé', 'Bouza', 'Illéla', 'Kéita', 'Madaoua', 'Malbaza', 'Tassara', 'Tchintabaraden', 'Tilia', 'Barmou', 'Binder', 'Déoulé', 'Galma', 'Gougaram', 'Ibohamane', 'Kalfou', 'Kao', 'Karakara', 'Kijigari', 'Kouré', 'Maliar', 'Makarau', 'Mouléla', 'N\'Gourti', 'Sabon Guida', 'Sarkin Aréwa', 'Sarkin Yamma', 'Tamaya', 'Tébaram', 'Téguir', 'Tombokoi', 'Tsernaoua', 'Wanzarbé', 'WaraméZinder']],
                ['nom' => 'Tillabéri', 'communes' => ['Tillabéri', 'Ayorou', 'Balleyara', 'Bankilaré', 'Filingué', 'Gothèye', 'Kollo', 'Ouallam', 'Say', 'Téra', 'Torodi', 'Abala', 'Anzourou', 'Banibangou', 'Bibiyergou', 'Dessa', 'Garbey Kourou', 'Hamdallaye', 'Kankani', 'Kouré', 'Kourfey', 'Kourteye', 'Makalondi', 'Namari', 'N\'Dounga', 'Ouankort', 'Sakoira', 'Sansanné-Haoussa', 'Sargadji', 'Simiri', 'Sona', 'Tagazar', 'Tamou', 'Tiantiégou', 'Tondikandia', 'Tondikiwindi', 'Youri', 'Yélou']],
                ['nom' => 'Zinder', 'communes' => ['Zinder', 'Gouré', 'Kantché', 'Magaria', 'Damagaram', 'Droum', 'Dungass', 'Falenko', 'Gaffati', 'Gamou', 'Gouchi', 'Gouna', 'Guidiguir', 'Hamdara', 'Kellé', 'Kiéché', 'Kouka', 'Moa', 'Olléléwa', 'Sassoumbroum', 'Takiéta', 'Tanda', 'Tané', 'Tirmini', 'Wacha', 'Yaouri', 'Yékoua', 'Zermou']],
            ],
        ],
        'Sénégal' => [
            'departements' => [
                ['nom' => 'Dakar', 'communes' => ['Dakar', 'Guédiawaye', 'Pikine', 'Rufisque', 'Keur Massar', 'Bargny', 'Sébikhotane', 'Yène', 'Diamnadio', 'Sangalkam', 'Jaxaay', 'Mbao', 'Thiaroye', 'Yeumbeul', 'Médina', 'Grand-Dakar', 'Fann-Point-E', 'Ngor', 'Ouakam', 'Mermoz-Sacré-Cœur', 'Hann-Bel-Air', 'Patte d\'Oie', 'Fann', 'Sicap', 'Liberté', 'Dieuppeul']],
                ['nom' => 'Diourbel', 'communes' => ['Diourbel', 'Bambey', 'Mbacké', 'Touba', 'Ndindy', 'N\'Dondol', 'Kaour', 'Taïf', 'Dankh', 'Mbeggé', 'Patar', 'Lambaye', 'Tèkène', 'Kael', 'Ndoulo', 'Ngom', 'Gossas']],
                ['nom' => 'Fatick', 'communes' => ['Fatick', 'Foundiougne', 'Gossas', 'Passy', 'Sokone', 'Diofior', 'Karane', 'Mpal', 'Niodior', 'Diakhao', 'Thiomby', 'Toubacouta', 'Dagathie', 'Mbar', 'N\'Diaffate', 'Patar', 'Tattaguine', 'Ngoundiane']],
                ['nom' => 'Kaffrine', 'communes' => ['Kaffrine', 'Birkelane', 'Koungheul', 'Malem Hodar', 'Mabo', 'Kahi', 'N\'Ganda', 'Ngoundioum', 'Sagna', 'Boulel', 'Daro', 'Keur Madiabel', 'Missirah', 'Médinatoul Salam', 'Gagnick', 'Keur Momar Sarr', 'Salick', 'Wassadou', 'Nganda', 'Khelcom']],
                ['nom' => 'Kaolack', 'communes' => ['Kaolack', 'Nioro du Rip', 'Guinguinéo', 'Koutal', 'N\'Doffane', 'Sibassor', 'Kahone', 'Latmingué', 'Thiomby', 'Fass', 'N\'Diébel', 'Mbédiène', 'Mbadakhoune', 'Ngane', 'Dya', 'Pâkala', 'Keur Baka', 'Keur Socé', 'Touba Mandakh', 'Diamaguène', 'Kagnobon']],
                ['nom' => 'Kédougou', 'communes' => ['Kédougou', 'Saraya', 'Salémata', 'Bembou', 'Dindéfelo', 'Bandafassi', 'Dimboli', 'Fongolembi', 'Kévé', 'N\'Débou', 'Sinthiou', 'Toura', 'Mako', 'Khossanto', 'Ségou', 'Sankarabaté', 'Sokotéla']],
                ['nom' => 'Kolda', 'communes' => ['Kolda', 'Médina Yoro Foulah', 'Vélingara', 'Dabo', 'Saré Bidi', 'Dioulacolon', 'Mampatim', 'Guinkin', 'Kounkané', 'Sinthiang', 'Bignona', 'Kafountine', 'Diouloulou', 'Niafourang', 'Tanaff', 'Djibanar', 'Kartiack']],
                ['nom' => 'Louga', 'communes' => ['Louga', 'Kébémer', 'Linguère', 'Dahra', 'Sakal', 'Mbeuleukhé', 'N\'Guenéne', 'Coki', 'Thiamène', 'Niomré', 'Loro', 'Barkédji', 'Déaly', 'Thièle', 'Boulal', 'Nguer', 'N\'Diayène', 'Téssékré', 'Ouarkhokh', 'Médina Ndiathbé', 'Kamb', 'Sylla', 'Gadé', 'Agnam']],
                ['nom' => 'Matam', 'communes' => ['Matam', 'Kanel', 'Ranérou', 'Ogo', 'Oréfondé', 'N\'Gourignyo', 'Sinthiou Bamamabé', 'Wawa', 'Thilogne', 'Bokidiawé', 'Agnam-Civol', 'Sadel', 'Lougré', 'Odobéré', 'Nabadji', 'Boulal', 'Sinthiou', 'Tambacounda']],
                ['nom' => 'Saint-Louis', 'communes' => ['Saint-Louis', 'Dagana', 'Podor', 'Richard-Toll', 'N\'Diayène', 'Gaé', 'Mpal', 'Rosso', 'Ndombo', 'Fanaye', 'Donaye', 'Bokhol', 'Khothor', 'Rao', 'Gandon', 'Fass', 'Diama', 'Mboumba', 'N\'Guette', 'Walaldé', 'Dodel', 'Galoya Toucouleur', 'Ndioum', 'Saldé', 'Guédé', 'Aéré', 'Méri', 'Thillé Boubakar', 'Taredji', 'Golléré', 'Pété', 'Nguith', 'Cas-Cas', 'Tiougoune', 'Mbane']],
                ['nom' => 'Sédhiou', 'communes' => ['Sédhiou', 'Bounkiling', 'Goudomp', 'Diattacounda', 'Djibabouya', 'Sansamba', 'Tanaff', 'Bélé', 'Koussy', 'Mangaroungou', 'Mansar', 'Mougnan', 'N\'Diamacouta', 'Saré', 'Tassine', 'Bambali', 'Diambéring', 'Kandion', 'M\'Bout', 'Témento', 'Yarang']],
                ['nom' => 'Tambacounda', 'communes' => ['Tambacounda', 'Bakel', 'Goudiry', 'Koumpentoum', 'Dianké', 'Ballou', 'Bambadinka', 'Boutoucoufara', 'Koulor', 'Madina', 'Makacollibantang', 'N\'Doga', 'N\'Diataya', 'Sinthiou', 'Toumboura', 'Nékhou', 'Maka', 'Kothiary', 'Bani', 'Bélé', 'Kougnenty', 'Département']],
                ['nom' => 'Thiès', 'communes' => ['Thiès', 'Mbour', 'Tivaouane', 'Popenguine', 'Saly', 'Joal-Fadiouth', 'Nianing', 'Sindia', 'Fandène', 'Pout', 'Keur Moussa', 'Kayar', 'Ngoundiane', 'N\'Diaganiao', 'Thiénaba', 'N\'Goudiane', 'Diass', 'Mékhé', 'Taïba', 'Mbar', 'Ngohé', 'Pékès', 'Bandia']],
                ['nom' => 'Ziguinchor', 'communes' => ['Ziguinchor', 'Bignona', 'Oussouye', 'Adéane', 'Boutoupa', 'Kaguitte', 'Mlomp', 'Niadior', 'Niomoune', 'Santiaba', 'Sindian', 'Soucouta', 'Tanaff', 'Diembéring', 'Kafountine', 'Balingor', 'Cobly', 'Djibelor', 'Enampor', 'Kartiack', 'Mangagoulack', 'Nyassia', 'Séléki']],
            ],
        ],
        'Togo' => [
            'departements' => [
                ['nom' => 'Maritime', 'communes' => ['Lomé', 'Tsévié', 'Agoe', 'Aného', 'Tabligbo', 'Baguida', 'Karakata', 'Kpogan', 'Togoville', 'Vogan', 'Grand-Popo', 'Attitogon', 'Davant', 'Sangué', 'Sagbado', 'Noépé', 'Kéwa', 'Kouvé', 'Sévagan', 'Afiadénigba', 'Djidjolé', 'Mafi', 'Batome', 'Kossikopé', 'Agou', 'Amlamé', 'Apeyémé', 'Atakpamé', 'Badou', 'Danyi', 'Dayes', 'Kpalimé', 'Kpélé', 'Kloto']],
                ['nom' => 'Plateaux', 'communes' => ['Atakpamé', 'Kpalimé', 'Badou', 'Amlamé', 'Danyi', 'Dayes', 'Kloto', 'Kpélé', 'Agou', 'Akebou', 'Anié', 'Est-Mono', 'Haho', 'Moyen-Mono', 'Ogou', 'Wawa', 'Amou', 'Tchamba']],
                ['nom' => 'Centrale', 'communes' => ['Sokodé', 'Sotouboua', 'Tchamba', 'Bafilo', 'Tchaoudjo', 'Assoli', 'Soussou', 'Mô', 'Blitta', 'Mô-Kodé', 'Pagouda', 'Kparatao', 'Kétao', 'Tembi', 'Léama', 'Boumbouaka', 'Sassara', 'Komah', 'Bissibo', 'Kabolo', 'Nganou', 'Sala', 'Sob']],
                ['nom' => 'Kara', 'communes' => ['Kara', 'Niamtougou', 'Bassar', 'Bafilo', 'Pagouda', 'Kéran', 'Doufelgou', 'Binah', 'Dankpen', 'Kozah', 'Assoli', 'Bassou', 'Kanté', 'Kétao', 'Lahou', 'Nangban', 'Saré', 'Farendé', 'Boukoumbé', 'Séméré', 'Kéta', 'Kalanga', 'Koufaré', 'Kpassou']],
                ['nom' => 'Savanes', 'communes' => ['Dapaong', 'Mango', 'Tandjouaré', 'Oti', 'Tône', 'Kpendjal', 'Cinkassé', 'Gando', 'Konkombou', 'Mandouri', 'Borgou', 'Naki-Est', 'Naki-Ouest', 'Tampialga', 'Sambia', 'Takpamba', 'Sassèné', 'Nogoudjouk', 'Lotogou', 'N\'Djaména', 'Guerin-Kouka', 'Kountoiré', 'Lokpanou', 'Miguina', 'Sotali', 'Talakom', 'Timbou', 'Tori', 'Koukoumbou', 'Ogaro']],
            ],
        ],
    ];

    public function run(): void
    {
        foreach ($this->countries as $countryData) {
            $pay = Pay::firstOrCreate(
                ['code' => $countryData['code']],
                ['nom' => $countryData['nom'], 'indicatif' => $countryData['indicatif'], 'actif' => true]
            );

            $departements = $this->geo[$countryData['nom']]['departements'] ?? [];

            foreach ($departements as $depData) {
                $departement = Departement::firstOrCreate(
                    ['pays_id' => $pay->id, 'nom' => $depData['nom']]
                );

                $communeNames = $depData['communes'] ?? [];

                foreach ($communeNames as $communeName) {
                    Commune::firstOrCreate(
                        ['departement_id' => $departement->id, 'nom' => $communeName]
                    );
                }
            }
        }

        // Create arrondissements and quartiers for Ouagadougou and Bobo-Dioulasso
        $this->seedArrondissementsEtQuartiers();
    }

    private function seedArrondissementsEtQuartiers(): void
    {
        $arrondissements = [
            ['commune' => 'Ouagadougou', 'arrondissements' => [
                'Baskuy' => ['Gounghin', 'Kamséogo', 'Koulouba', 'Larlé', 'Nimbi', 'Paspanga', 'Saint-Léon', 'Siguinvoussé', 'Tampouy', 'Zabré', 'Zénith', 'Zone du Bois', 'Dapoya', 'Kouritenga', 'Ouidi', 'Samandin', 'Tientsabo', 'Yagma', 'Bilbalogo', 'Cissin', 'Hamdalaye', 'Kamsi', 'Kildi-Yarce', 'Kourima', 'Mogho Naaba', 'Nabéré', 'Ouahigouya', 'Pincé', 'Roumtenga', 'Sainte-Famille', 'Somgandé', 'Tanghin', 'Tara', 'Tindé-Yarce', 'Trame d\'Accueil', 'Wayalghin', 'Yempéogo', 'Yimdi'],
                'Bogodogo' => ['Bagr-Nooma', 'Bendogo', 'Bogodogo', 'Dagnonghin', 'Dassissin', 'Dassui', 'Gandiin', 'Goab-Marka', 'Gog-Zoungou', 'Kamboincé', 'Kamboincé-Fine', 'Karpala', 'Kilwin', 'Kombemba', 'Koumandé', 'Kouritenga', 'Larlé', 'Mankoala', 'Nabab', 'Nabalogo', 'Nawettéba', 'Nibiogo', 'Nioko', 'Nonyalé', 'Ouaga 2000', 'Paspanga', 'Pazanni', 'Pissy', 'Ponba', 'Sainte-Suzanne', 'Sakoula', 'Sanga', 'Satoro', 'Saya', 'Siby', 'Sondogo', 'Song-Taaba', 'Sonyalé', 'Sopatim', 'Tampouy', 'Tanghin', 'Tansobentinga', 'Targa-Yanga', 'Tchériba', 'Tendologo', 'Tindibogodin', 'Tounougen', 'Warga', 'Yagma', 'Yamtaoré', 'Yemeni'],
                'Boulmiougou' => ['Boulmiougou', 'Bissighin', 'Dagnonghin', 'Dapélogo', 'Dassissin', 'Dassui', 'Delestin', 'Dima', 'Goetma', 'Goetma-Silmi', 'Goetma-Sud', 'Goundouba', 'Hamdalaye', 'Kamboincé', 'Kamboincé-Fine', 'Karpala', 'Kassoumbarga', 'Kilwin', 'Kolog-Naba', 'Kolog-Nodé', 'Komkoulibou', 'Kouba', 'Koulouba', 'Ladjim', 'Larlé', 'Mami', 'Mankoala', 'Mohicour', 'Nabalogo', 'Nanou', 'Nééré', 'Nimbrilé', 'Nimdi', 'Nonghin', 'Ouayissi', 'Pabré', 'Pagou', 'Palesgo', 'Sambin', 'Sandogo', 'Sanga', 'Silmiougou', 'Somgandé', 'Song-Mané', 'Song-Taaba', 'Tanghin', 'Tansobentinga', 'Targa-Yanga', 'Tchériba', 'Tendologo', 'Tind-Sablogo', 'Tind-Yaméogo', 'Tindibogodin', 'Tounougen', 'Voaga', 'Warga', 'Wemya', 'Yagma', 'Yamtaoré', 'Yemeni', 'Yougbila'],
                'Nongremasson' => ['Nongremasson', 'Bissighin', 'Bogodogo', 'Dagnonghin', 'Dassissin', 'Dima', 'Gandiin', 'Goab-Marka', 'Goetma', 'Gog-Zoungou', 'Goundouba', 'Hamdalaye', 'Kamboincé', 'Kamboincé-Fine', 'Karpala', 'Kassoumbarga', 'Kilwin', 'Kolog-Naba', 'Kolog-Nodé', 'Komkoulibou', 'Kouba', 'Koulouba', 'Ladjim', 'Larlé', 'Mami', 'Mankoala', 'Nabalogo', 'Nanou', 'Nimbrilé', 'Nimdi', 'Nonghin', 'Ouayissi', 'Pabré', 'Pagou', 'Palesgo', 'Pissy', 'Ponba', 'Sainte-Suzanne', 'Sakoula', 'Sambin', 'Sandogo', 'Sanga', 'Satoro', 'Saya', 'Siby', 'Silmiougou', 'Sondogo', 'Song-Taaba', 'Sonyalé', 'Sopatim', 'Tampouy', 'Tanghin', 'Tansobentinga', 'Tara', 'Targa-Yanga', 'Tchériba', 'Tendologo', 'Tind-Yaméogo', 'Tindibogodin', 'Tindé-Yarce', 'Tounougen', 'Voaga', 'Warga', 'Wayalghin', 'Wemya', 'Yagma', 'Yamtaoré', 'Yemeni', 'Yimdi', 'Yougbila', 'Zabré', 'Zénith', 'Zone du Bois'],
                'Sig-Noghin' => ['Sig-Noghin', 'Bendogo', 'Bilbalogo', 'Bissighin', 'Cissin', 'Dagnonghin', 'Dapélogo', 'Dassissin', 'Dima', 'Gandiin', 'Goetma', 'Gog-Zoungou', 'Goundouba', 'Hamdalaye', 'Kamboincé', 'Kamboincé-Fine', 'Karpala', 'Kassoumbarga', 'Kilwin', 'Kolog-Naba', 'Kombemba', 'Komkoulibou', 'Kouba', 'Koulouba', 'Ladjim', 'Larlé', 'Mami', 'Mankoala', 'Nabalogo', 'Nanou', 'Nimbrilé', 'Nimdi', 'Nonghin', 'Ouayissi', 'Pabré', 'Pagou', 'Palesgo', 'Pissy', 'Ponba', 'Sakoula', 'Sambin', 'Sandogo', 'Sanga', 'Saya', 'Siby', 'Silmiougou', 'Somgandé', 'Sondogo', 'Song-Taaba', 'Sonyalé', 'Sopatim', 'Tampouy', 'Tanghin', 'Tansobentinga', 'Tara', 'Targa-Yanga', 'Tchériba', 'Tendologo', 'Tind-Yaméogo', 'Tindibogodin', 'Tounougen', 'Voaga', 'Warga', 'Wayalghin', 'Wemya', 'Yagma', 'Yamtaoré', 'Yemeni', 'Yimdi', 'Yougbila', 'Zabré', 'Zénith'],
            ]],
            ['commune' => 'Bobo-Dioulasso', 'arrondissements' => [
                'Dafra' => ['Accueil', 'Banakélédaga', 'Béas', 'Bohô', 'Bobinna', 'Bolmakoté', 'Boulouba', 'Boyasso', 'Bozo', 'Bwaba', 'Coco', 'Compte-1', 'Compte-2', 'Dafra', 'Darsalamy', 'Dioulaba', 'Dogona', 'Dogona-B', 'Dogona-Nord', 'Dogona-Sud', 'Farakan', 'Gassèlè', 'Gouana', 'Gouana-Sud', 'Gouni', 'Guimbi', 'Habrou', 'Habrou-Sud', 'Hèrèmakono', 'Kafigué', 'Kafigué-Sud', 'Kafigué-Ouest', 'Kafigué-Est', 'Kafigué-Centre', 'Kafigué-Nord', 'Kafigué-Sud-Est', 'Koko', 'Koko-Sud', 'Koko-Ouest', 'Koko-Nord', 'Koko-Centre', 'Komio', 'Koua', 'Koua-Sud', 'Koua-Ouest', 'Koumi', 'Koumna', 'Koumna-Sud', 'Lafiabougou', 'Mafasso', 'Mama', 'Mama-Sud', 'Mama-Ouest', 'Mama-Nord', 'Mama-Centre', 'Mandiéla', 'Médina', 'Nafadè', 'Nasso', 'Néma', 'Népéssé', 'Niénéba', 'Nikin', 'Pala', 'Papa', 'Papa-Sud', 'Papa-Ouest', 'Papa-Nord', 'Papa-Centre', 'Patte-D\'Oie', 'Sakala', 'Sambana', 'Sambana-Sud', 'Sanguéra', 'Séguéra', 'Séguéna', 'Seydou', 'Sokoura', 'Sokoura-Sud', 'Sokouraba', 'Soumousso', 'Tahiri', 'Taï', 'Tere', 'Tessin', 'Tigou', 'Tindikilé', 'Tindikilé-Sud', 'Tindikilé-Ouest', 'Tindikilé-Nord', 'Tindikilé-Centre', 'Tinn', 'Toudo', 'Wédou', 'Wédou-Sud', 'Wédou-Ouest', 'Yéguélé', 'Yohogo'],
                'Dogona' => ['Affecté', 'Bana', 'Béas', 'Bohô', 'Bona', 'Bouna', 'Bouzourou', 'Bozo', 'Bwaba', 'Coco', 'Dafra', 'Darsalamy', 'Dioulaba', 'Dogona', 'Dogona-B', 'Dogona-Nord', 'Dogona-Sud', 'Farakan', 'Gassèlè', 'Gouana', 'Gouana-Sud', 'Gouni', 'Guimbi', 'Habrou', 'Habrou-Sud', 'Hèrèmakono', 'Kafigué', 'Kafigué-Sud', 'Kafigué-Ouest', 'Kafigué-Est', 'Kafigué-Centre', 'Kafigué-Nord', 'Kafigué-Sud-Est', 'Koko', 'Koko-Sud', 'Koko-Ouest', 'Koko-Nord', 'Koko-Centre', 'Komio', 'Koua', 'Koua-Sud', 'Koua-Ouest', 'Koumi', 'Koumna', 'Koumna-Sud', 'Lafiabougou', 'Mafasso', 'Mama', 'Mama-Sud', 'Mama-Ouest', 'Mama-Nord', 'Mama-Centre', 'Mandiéla', 'Médina', 'Nafadè', 'Nasso', 'Néma', 'Népéssé', 'Niénéba', 'Nikin', 'Pala', 'Papa', 'Papa-Sud', 'Papa-Ouest', 'Papa-Nord', 'Papa-Centre', 'Patte-D\'Oie', 'Sakala', 'Sambana', 'Sambana-Sud', 'Sanguéra', 'Séguéra', 'Séguéna', 'Seydou', 'Sokoura', 'Sokoura-Sud', 'Sokouraba', 'Soumousso', 'Tahiri', 'Taï', 'Tere', 'Tessin', 'Tigou', 'Tindikilé', 'Tindikilé-Sud', 'Tindikilé-Ouest', 'Tindikilé-Nord', 'Tindikilé-Centre', 'Tinn', 'Toudo', 'Wédou', 'Wédou-Sud', 'Wédou-Ouest', 'Yéguélé', 'Yohogo'],
            ]],
        ];

        foreach ($arrondissements as $data) {
            $commune = Commune::where('nom', $data['commune'])->first();
            if (! $commune) {
                continue;
            }

            foreach ($data['arrondissements'] as $arrondissementNom => $quartiers) {
                $arrondissement = Arrondissement::firstOrCreate([
                    'commune_id' => $commune->id,
                    'nom' => $arrondissementNom,
                ]);

                foreach ($quartiers as $quartierNom) {
                    Quartier::firstOrCreate([
                        'arrondissement_id' => $arrondissement->id,
                        'nom' => $quartierNom,
                    ]);
                }
            }
        }
    }
}
