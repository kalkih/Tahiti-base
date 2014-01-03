<?php
/**
 * Calender class
 *
 */
class CCalendar {

    // Variables
    private $year;
    public $month;
    private $day;

    // Constructor
    public function __construct($year = 0, $month = 0, $day = 0) {

        if ($year == 0) {
            $this->year = date("Y");
        }
        else {
            $this->year = $year;
        }

        if ($month == 0) {
            $this->month = date("n");
        }
        else {
            $this->month = $month;
        }

        if ($day == 0) {
            $this->day = date("j");
        }
        else {
            $this->day = $day;
        }

    }

    public function GetCalendar($title = 0) {
        $week = $this->GetWeek();
        $html = null;
        $html .= '<table class="calendar">';

        if ($title != 0) {
            $html .= '<tr><th COLSPAN="8" class="title">' . $this->GetMonthName($this->month) . '</th></tr>';
        }

        $html .= '<tr><th></th><th>Måndag</th><th>Tisdag</th><th>Onsdag</th><th>Torsdag</th><th>Fredag</th><th>Lördag</th><th style="color: red;">Söndag</th></tr>';
        $html .= '<tr><th>' . $week . '</th>';

        $cellCounter = 0;

        for ($i = 0; $i < $this->GetPrevDays(); $i++) { 
            $html .= '<td class="out-focus"></td>';
            $cellCounter++;
        }

        for ($day = 1; $day <= $this->GetMonthDays(); $day++) { 
            $class = null;
            
            if ($cellCounter == 7) {
                $html .= '</tr><tr><th>' . ++$week . '</th>';
                $cellCounter = 0;
            }

            if ($cellCounter == 6) {
                $class .= ' red-day';
            }

            if ($this->day != 1 && $this->day == $day) {
                $class .= ' current-day';
            }

            $html .= "<td class='in-focus$class'><span class='day'>" . $day . '</span><p class="nameday">' . $this->GetNameDay($this->month, $day) . '</p></td>';
            $cellCounter++;
        }

        while ($cellCounter < 7) {
            $html .= '<td class="out-focus"></td>';
            $cellCounter++;
        }
        
        
        $html .= '</tr></table>';

        return $html;
    }

    public function GetMonthDays() {
        return date('t', strtotime($this->year . '-' . $this->month . '-' . '1'));
    }

    public function GetDay() {
        return $this->day;
    }

    public function GetWeek() {
        return date('W', strtotime($this->year . '-' . $this->month . '-' . '1'));
    }

    public function GetMonth() {
        return $this->month;
    }

    public function GetMonthName($month) {
        $months = array('1'=>'Januari', '2'=>'Februari', '3'=>'Mars', '4'=>'April', '5'=>'Maj', '6'=>'Juni', '7'=>'Juli', '8'=>'Augusti', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'December');
        return $months[$month];
    }

    public function GetYear() {
        return $this->year;
    }

    public function GetDate() {
        return $this->year . '-' .  $this->month . '-' . $this->day;
    }

    public function GetPrevDays() {
        $weekDay = date('l', strtotime($this->year . '-' . $this->month . '-' . '1'));

        switch ($weekDay) {

            case 'Monday':
                $days = 0;
                break;

            case 'Tuesday':
                $days = 1;
                break;

            case 'Wednesday':
                $days = 2;
                break;

            case 'Thursday':
                $days = 3;
                break;

            case 'Friday':
                $days = 4;
                break;

            case 'Saturday':
                $days = 5;
                break;

            case 'Sunday':
                $days = 6;
                break;

            default:

                break;
        }

        return $days;
    }

    public function GeneratePrevMonthLink() {
        $year = null;
        $month = null;
        if ($this->month == 1) {
            $year = $this->year - 1;
            $month = 12;
        }
        else {
            $year = $this->year;
            $month = $this->month - 1;
        }
        return '<a href="?year=' . $year . '&amp;month=' . $month .'">◄' . '</a>';
    }

    public function GenerateNextMonthLink() {
        $year = null;
        $month = null;
        if ($this->month == 12) {
            $year = $this->year + 1;
            $month = 1;
        }
        else {
            $year = $this->year;
            $month = $this->month + 1;
        }
        return '<a href="?year=' . $year . '&amp;month=' . $month .'">' . '►</a>';
    }

    public function GetNameDay($month, $day) {
        $nameDay = array('',
            array('',"Nyårsdagen", "Svea", "Alfred, Alfrida", "Rut", "Hanna, Hannele", "Kasper, Melker, Baltsar /13-dag jul", "August, Augusta", "Erland", "Gunnar, Gunder", "Sigurd, Sigbritt", "Jan, Jannike", "Frideborg, Fridolf", "Knut / Tjugondedag jul", "Felix, Felicia", "Laura, Lorentz", "Hjalmar, Helmer", "Anton, Tony", "Hilda, Hildur", "Henrik", "Fabian, Sebastian", "Agnes, Agneta", "Vincent, Viktor", "Frej, Freja", "Erika", "Paul, Pål", "Bodil, Boel", "Göte, Göta", "Karl, Karla", "Diana", "Gunilla, Gunhild", "Ivar, Joar"),
            array('',"Max, Maximilian", "Kyndelsmässo-dagen", "Disa, Hjördis", "Ansgar, Anselm", "Agata, Agda", "Dorotea, Doris", "Rikard, Dick", "Berta, Bert", "Fanny, Franciska", "Iris", "Yngve, Inge", "Evelina, Evy", "Agne, Ove", "Valentin", "Sigfrid", "Julia, Julius", "Alexandra, Sandra", "Frida, Fritiof", "Gabriella, Ella", "Vivianne", "Hilding", "Pia", "Torsten, Torun", "Mattias, Mats", "Sigvard, Sivert", "Torgny, Torkel", "Lage", "Maria", "Skottdagen"),
            array('',"Albin, Elvira", "Ernst, Erna", "Gunborg, Gunvor", "Adrian, Adriana", "Tora, Tove", "Ebba, Ebbe", "Camilla", "Siv", "Torbjörn, Torleif", "Edla, Ada", "Edvin, Egon", "Viktoria", "Greger", "Matilda, Maud", "Kristoffer, Christel", "Herbert, Gilbert", "Gertrud", "Edvard, Edmund", "Josef, Josefina", "Joakim, Kim", "Bengt", "Kennet, Kent", "Gerda, Gerd", "Gabriel, Rafael", "Marie bebådelsedag", "Emanuel", "Rudolf, Ralf", "Malkolm, Morgan", "Jonas Jens", "Holger Holmfrid", "Ester"),
            array('',"Harald, Hervor", "Gudmund, Ingeund", "Ferdinand, Nanna", "Marianne, Marlene", "Irene, Irja", "Vilhelm, Helmi", "Irma, Irmelin", "Nadja, Tanja", "Otto, Ottilia", "Ingvar, Ingvor", "Ulf, Ylva", "Liv", "Artur, Douglas", "Tiburtius", "Olivia, Oliver", "Patrik, Patricia", "Elias, Elis", "Valdemar, Volmar", "Olaus, Ola", "Amalia, Amelie", "Anneli, Annika", "Allan, Glenn", "Georg, Göran", "Vega", "Markus", "Teresia, Terese", "Engelbrekt", "Ture, Tyra", "Tyko", "Mariana"),
            array('',"Valborg", "Filip, Filippa", "John, Jane", "Monika, Mona", "Gotthard, Erhard", "Marit, Rita", "Carina, Carita", "Åke", "Reidar, Reidun", "Esbjörn, Styrbjörn", "Märta, Märit", "Charlotta, Lotta", "Linnea, Linn", "Halvard, Halvar", "Sofia, Sonja", "Ronald, Ronny", "Rebecka, Ruben", "Erik", "Maj, Majken", "Karolina, Carola", "Konstantin, Conny", "Hemming, Henning", "Desideria, Desiree", "Ivan, Vanja", "Urban", "Vilhelmina, Vilma", "Beda, Blenda", "Ingeborg, Borghild", "Yvonne, Jeanette", "Vera, Veronika", "Petronella, Pernilla"),
            array('',"Gun, Gunnel", "Rutger, Roger", "Ingemar, Gudmar", "Solbritt, Solveig", "Bo", "Gustav, Gösta", "Robert, Robin", "Eivor, Majvor", "Börje, Birger", "Svante, Boris", "Bertil, Berthold", "Eskil", "Aina, Aino", "Håkan, Hakon", "Margit, Margot", "Axel, Axelina", "Torborg, Torvald", "Björn, Bjarne", "Germund, Görel", "Linda", "Alf, Alvar", "Paulina, Paula", "Adolf, Alice", "Johannes Döparens dag", "David, Salomon", "Rakel, Lea", "Selma, Fingal", "Leo", "Peter, Petra", "Elof, Leif"),
            array('',"Aron, Mirjam", "Rosa, Rosita", "Aurora", "Ulrika, Ulla", "Laila, Ritva", "Esaias, Jessika", "Klas", "Kjell", "Jörgen, Örjan", "Andre, Andrea", "Eleonora, Ellinor", "Herman, Hermine", "Joel, Judit", "Folke", "Ragnhild, Ragnvald", "Reinhold, Reine", "Bruno", "Fredrik, Fritz", "Sara", "Margareta, Greta", "Johanna", "Magdalena, Madeleine", "Emma", "Kristina, Kerstin", "Jakob", "Jesper", "Marta", "Botvid, Seved", "Olof", "Algot", "Helena, Elin"),
            array('',"Per", "Karin, Kajsa", "Tage", "Arne, Arnold", "Ulrik, Alrik", "Alfons, Inez", "Dennis, Denise", "Silvia, Sylvia", "Roland", "Lars", "Susanna", "Klara", "Kaj", "Uno", "Stella, Estelle", "Brynolf", "Verner, Valter", "Ellen, Lena", "Magnus, Måns", "Bernhard, Bernt", "Jon, Jonna", "Henrietta, Henrika", "Signe, Signhild", "Bartolomeus", "Lovisa, Louise", "Östen", "Rolf, Raoul", "Gurli, Leila", "Hans, Hampus", "Albert, Albertina", "Arvid, Vidar"),
            array('',"Samuel", "Justus, Justina", "Alfhild, Alva", "Gisela", "Adela, Heidi", "Lilian, Lilly", "Regina, Roy", "Alma, Hulda", "Anita, Annette", "Tord, Turid", "Dagny, Helny", "Åsa, Åslög", "Sture", "Ida", "Sigrid, Siri", "Dag, Daga", "Hildegard, Magnhild", "Orvar", "Fredrika", "Elise, Lisa", "Matteus", "Maurits, Moritz", "Tekla, Tea", "Gerhard, Gert", "Tryggve", "Enar, Einar", "Dagmar, Rigmor", "Lennart, Leonard", "Mikael, Mikaela", "Helge"),
            array('',"Ragnar, Ragna", "Ludvig, Love", "Evald, Osvald", "Frans, Frank", "Bror", "Jenny, Jennifer", "Birgitta, Britta", "Nils", "Ingrid, Inger", "Harry, Harriet", "Erling, Jarl", "Valfrid, Manfred", "Berit, Birgit", "Stellan", "Hedvig, Hillevi", "Finn", "Antonia, Toini", "Lukas", "Tore, Tor", "Sibylla", "Ursula, Yrsa", "Marika, Marita", "Severin, Sören", "Evert, Eilert", "Inga, Ingalill", "Amanda, Rasmus", "Sabina", "Simon, Simone", "Viola", "Elsa, Isabella", "Edit, Edgar"),
            array('',"Allhelgonadagen", "Tobias", "Hubert, Hugo", "Sverker", "Eugen, Eugenia", "Gustav, Adolf", "Ingegerd, Ingela", "Vendela", "Teodor, Teodora", "Martin Martina", "Mårten,", "Konrad, Kurt", "Kristian, Krister", "Emil, Emilia", "Leopold", "Vibeke, Viveka", "Naemi, Naima", "Lillemor, Moa", "Elisabet, Lisbet", "Pontus, Marina", "Helga, Olga", "Cecilia, Sissela", "Klemens", "Gudrun, Rune", "Katarina, Katja", "Linus", "Astrid, Asta", "Malte", "Sune", "Andreas, Anders"),
            array('',"Oskar, Ossian", "Beata, Beatrice", "Lydia", "Barbara, Barbro", "Sven", "Nikolaus, Niklas", "Angela, Angelika", "Virginia", "Anna", "Malin, Malena", "Daniel, Daniela", "Alexander, Alexis", "Lucia", "Sten Sixten", "Gottfrid", "Assar", "Stig", "Abraham", "Isak", "Israel, Moses", "Tomas", "Natanael, Jonatan", "Adam", "Eva", "Juldagen", "Stefan, Staffan", "Johannes, Johan", "Benjamin / Värnlösa barns dag", "Natalia, Natalie", "Abel, Set", "Sylvester")
        );
        return $nameDay[$month][$day];
    }
}

?>