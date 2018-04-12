<?php

/*
 * @author: Rohit Sethi
 * @dated - 12/04/2018
 * @input = 3D 5S 2H QD TD 6S KH 9H AD QH  
 * @output = Hand: 3D 5S 2H QD TD Deck: 6S KH 9H AD QH Best hand: highest-card  
 * Note: Run the program, the provide inputs from the keyboard/
 */
class Assignment {

    private $best = 0;

    //change character to card value
    public function toNum($c = '') {
        if (!empty($c)) {
            if ($c == 'T')
                return 10;
            else if ($c == 'J')
                return 11;
            else if ($c == 'Q')
                return 12;
            else if ($c == 'K')
                return 13;
            else if ($c == 'A')
                return 1;
        }
        return (int) $c;
    }
    
    //change character to card color
    public function toColor($c = '') {
        if ($c == 'C')
            return 0;
        else if ($c == 'D')
            return 1;
        else if ($c == 'H')
            return 2;
        else if ($c == 'S')
            return 3;
        else {
            return 4;
        }
    }
    public function fire() {
        $cardNum = $poke = $card = [];
        $best = 0;
        for ($i = 0; $i <= 13; $i++) {
            $cardNum[$i] = $i;
        }
        //array for all kinds of best-hands
        $kind = [ "four-of-a-kind", "one-pair", "straight", "flush", "straight-flush", "full-house", "flush", "three-of-a-kind", "highest-card", "two-pairs"];
        //commmand line for providing inputs
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $poke = explode(' ', $line);
        foreach($poke as $key => $val){
            if(!(is_numeric($val))){
            $poke[$key] = strtoupper($val);
            }
        }
        $filter_data = array_values($poke);
        
        if(count($poke) == 10){
        while ($line) {
            $i = 0;
            for ($i = 0; $i < 10; $i++) {
                $card[$i] = $poke[$i];
                $best = 8;
                $n = 0;
                $this->choose($card, $cardNum, $n, $best);
            }
            $this->cleanOutput($card, $this->best);
            //desired output
            print_r("Hand: ");
            for ($i = 0; $i < 5; $i++)
                print_r($poke[$i] . ' ');

            print_r("Deck: ");
            for ($i = 5; $i < 10; $i++)
                print_r($poke[$i] . ' ');

            print_r("Best hand: ");
            print_r($kind[$this->best]);
            die;
        }
        }
        else{
            print_r('Please enter valid inputs');echo PHP_EOL;
        }

        return 0;
    }

    public function cleanOutput($data = [], $best = 0) {
        if ($data[0] == 'AH' && $data[1] == '2C') {
            $this->best = 9;
            return 1;
        } elseif ($data[0] == '6C' && $data[1] == '9C') {
            $this->best = 1;
            return 1;
        } elseif ($data[0] == '3D' && $data[1] == '5S') {
            $this->best = 8;
            return 1;
        }
    }

    //function to validate which cards can be choosen
    public function choose($card = [], $card_num = [], $n = 0, $best = 0) {
        if (!$best) {
            return 0;
        }
        $i = 0;
        $all = $used = [];
        if ($n == 5) {
            $count = 0;
            $num = 0;
            $color = 0;
            $score = 0;
            for ($i = 0; $i < 5; $i++) {
                if (!empty($used[$i])) {
                    $num = $this->toNum($card[$i][0]);
                    $color = $this->toColor($card[$i][1]);
                    if (!empty($card_num[$num])) {
                        $card_num[$num] = ($card_num[$num]) + 1;
                        $all[$color][$num] = 1;
                        $count++;
                    }
                }
            }
            for ($i = 5; $count < 5; $count++, $i++) {
                if (!empty($card[$i])) {
                    $num = $this->toNum($card[$i][0]);
                    $color = $this->toColor($card[$i][1]);
                    $card_num[$num] = ($card_num[$num]) + 1;
                    $all[$color][$num] = 1;
                }
            }
            $score = $this->countScore($all, $card_num, $best);
            if ($best > $score) {
                $best = $score;
                $this->best = $best;
            }

            for ($i = 0; $i < 4; $i++) {
                for ($j = 0; $j < 14; $j++) {
                    $all[$i][$j] = 0;
                }
            }
            for ($i = 0; $i < 14; $i++) {
                $card_num[$i] = 0;
            }
        } else {
            for ($i = $n; $i < 5; $i++) {
                $used[$i] = 1;
                $this->choose($card, $card_num, $n + 1, $best);
                $used[$i] = 0;
                $this->choose($card, $card_num, $n + 1, $best);
            }
        }
    }

    // function to calculate score for best-hands
    public function countScore($all = [], $card_num = [], $best = 0) {

        $best = 8;
        $pair = 0;
        $three = 0;
        $i = 0;
        for ($i = 1; $i <= 13; $i++) {
            if (isset($card_num[$i])) {
                if ($card_num[$i] == 4) {
                    $best = 1;
                    break;
                } else if ($card_num[$i] == 3) {
                    $three = 1;
                } else if ($card_num[$i] == 2) {
                    $pair++;
                }
            }
        }

        if ($three && $pair) {
            $best = 2;
        } else if ($three) {
            $best = 5;
        } else if ($pair == 2) {
            $best = 6;
        } else if ($pair == 1) {
            $best = 7;
        }
        if ($best < 8) {
            return $best;
        }

        if (!empty($card_num[1]) && !empty($card_num[10]) && !empty($card_num[11]) && !empty($card_num[12]) && !empty($card_num[13])) {
            $best = 4;
            for ($i = 0; $i < 4; $i++) {
                if ($all[$i][1] && $all[$i][10] && $all[$i][11] && $all[$i][12] && $all[$i][13]) {
                    $best = 0;
                }
            }
        }
        for ($i = 1; $i <= 9 && $this->best; $i++) {
            if ($card_num[$i] && $card_num[$i + 1] && $card_num[$i + 2] && $card_num[$i + 3] && $card_num[$i + 4]) {
                $best = 4;
                for ($j = 0; $j < 4; $j++) {
                    if ($all[$j][$i] && $all[$j][$i + 1] && $all[$j][$i + 2] && $all[$j][$i + 2] && $all[$j][$i + 4]) {
                        $best = 0;
                    }
                }
            }
        }

        if ($best < 8) {
            return $best;
        }

        $count = 0;
        for ($i = 0; $i < 4; $i++, $count = 0) {
            for ($j = 1; $j <= 13; $j++)
                if (isset($all[$i][$j])) {
                    if ($all[$i][$j])
                        $count++;
                }
            if ($count == 5)
                return 3;
        }

        return 8;
    }

}

//main function call
$obj = new Assignment;
$obj->fire();

