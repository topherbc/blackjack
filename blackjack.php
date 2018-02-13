<!DOCTYPE html>
<html>
    <head>

        <title>Auto-Blackjack</title>
        <link rel="stylesheet" href="css/style.css" type="text/css">

    </head>
    <body>
    <p>
    
        <div id='test'><h3 style="color:#404040">Please select an amount you would like your player to start with</h3></div>
        <form method="post">
            <input type="text" name="user" placeholder="User Name"></input>
            <button type="submit" name="Bet" value="100" class="myButton">$100</button>
            <button type="submit" name="Bet" value="250" class="myButton">$250</button>
            <button type="submit" name="Bet" value="1000" class="myButton">$1000</button>
            <button type="submit" name="Bet" value="9001" class="myButton">$9001</button>
        </form>
        
        <form method="post" id="userCardSelection" style="display:'hidden';">
            
        </form>
        <?php
             function createDeck() {
                 
                $suits = array ("clubs", "diamonds", "hearts", "spades");
                $faces = array (
                    "a" => 1, "2" => 2,"3" => 3, "4" => 4, "5" => 5, "6" => 6, "7" => 7,
                    "8" => 8, "9" => 9, "10" => 10, "j" => 10, "q" => 10, "k" => 10
                );
            
                $deck = array();
              
                foreach($suits as $suit){
                    foreach($faces as $value=>$face){
                        $newKey = $value .'of'. $suit;
                        $deck[$newKey] = $face;
                        
                    }
                }
                
                return $deck;
                    
            }
            
            //echo $_POST['value'];
            $deck = createDeck($deck);
            
            function rearrange($array){
                $keys = array_keys( $array );
                shuffle( $keys );
                return array_merge( array_flip( $keys ) , $array );
            }
            
            $deck = rearrange($deck);
            
            
            
            // v Checking stuff
            //var_dump($deck); exit;
            
            //cards totals and money and sorts
            $ptotal = 0;

            $dtotal = 0;
            
            $player = $_POST['user'];
            
            //$pmoney = 100;
            $pmoney = $_POST['Bet'];
            $original = $pmoney;
            $winround = $original;
            $bet = $original*.25;
            
            
            $round = 0;
            $blackjack = 0;
            
            //replaces values in array for the probability to count cards            
            function replaceValue($array){
                foreach($array as $key=>$value){
                    switch ($value) {
                        case 1:
                        case 10:
                            $array[$key] = -1;
                            break;
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                            $array[$key] = 1;
                            break;
                        case 7:
                        case 8:
                        case 9:
                            $array[$key] = 0;
                            break;
                    }
                }
                return $array;
            }
            //like that movie 21 or rain man or almost every gambling movie that is centered arround a genius
            function countCards($deck){
                $countCardsArray = replaceValue($deck);
                $talley = array_sum($countCardsArray);
                
                //var_dump($countCardsArray);
                //echo $talley . "</br>";
                
                return $talley;
            }
            
            //as if the dealer is a god and no mortal can ever beat him
            function dealerCountCards($deck){
                $countDealerArray = replaceValue($deck);
                $talley = array_sum($countDealerArray);
                
                //var_dump($countDealerArray);
                //echo $talley . "</br>";
                
                return $talley;
                
            }
            
            //making the array one array after the array_kshift makes it multidimensional
            function array_flatten($array) { 
              if (!is_array($array)) { 
                return FALSE; 
              } 
              $result = array(); 
              foreach ($array as $key => $value) { 
                if (is_array($value)) { 
                  $result = array_merge($result, array_flatten($value)); 
                } 
                else { 
                  $result[$key] = $value; 
                } 
              } 
              return $result; 
            } 

            //removal of cards and keeping the key intact
            function array_kshift(&$arr){
                reset($arr);
                $return = array(key($arr)=>current($arr));
                unset($arr[key($arr)]);
                return $return; 
            }
            
            //gives all the cards to the players
            function assignCards($type, &$deck){
                $playersCardsArray=[];
                $dealersCardsArray=[];
                
                if($type == 'player'){
                    

                    
                    $playersCardsArray[] = array_kshift($deck);
                    $playersCardsArray[] = array_kshift($deck);
                    $playersCardsArray = array_flatten($playersCardsArray);
                    

                    //var_dump($playersCardsArray);
                    //exit;
                    
                    foreach($playersCardsArray as $c){
                        foreach($playersCardsArray as $key => $card){
                            if($card == 1 && array_sum($playersCardsArray) - 1 <= 10){
                                $playersCardsArray[$key] = 11; 
                            }
                        }
                        
                        $talley = countCards($deck);
                        
                        //echo $talley . "<br>";
                        if(array_sum($playersCardsArray) <= 16 && $talley >= 0 || array_sum($playersCardsArray) <= 12 && count($deck) > 1){
                            $playersCardsArray[] = array_kshift($deck);
                            $playersCardsArray = array_flatten($playersCardsArray);
                        }
                    }
                    
                    echo "<h3 class='ptitle' >$players Cards</h3></br>";
                    
                    foreach($playersCardsArray as $key=>$value){
                        echo "<img class='cards' src=".'cards/'. $key . ".png alt=" .$key. "></img>";
                    }
                    echo "</br></br>";
                    //var_dump($deck);
                    //echo array_sum($playersCardsArray);
                    //var_dump($playersCardsArray);
                    return array_sum($playersCardsArray);

                    
                }
                if($type == 'dealer'){
                    $dealersCardsArray[] = array_kshift($deck);
                    $dealersCardsArray[] = array_kshift($deck);
                    $dealersCardsArray = array_flatten($dealersCardsArray);
                    
                    //var_dump($playersCardsArray);
                    //exit;
                    foreach($dealersCardsArray as $d){
                        foreach($dealersCardsArray as $key => $card){
                            if($card == 1 && array_sum($dealersCardsArray) - 1 <= 10){
                                $dealersCardsArray[$key] = 11; 
                            }
                        }
                        $dealerTalley = dealerCountCards($deck);
                        if(array_sum($dealersCardsArray) <= 17 && $dealerTalley >= 0 && count($deck) > 1){
                            $dealersCardsArray[] = array_kshift($deck);
                            $dealersCardsArray = array_flatten($dealersCardsArray);
                        }
                    }
                    
                    $sum = array_sum($dealersCardsArray);
                    
                    //var_dump($dealersCardsArray);
                    echo "<h3 class='dtitle'>Dealers Cards</h3></br>";
                    
                    echo "<img class='cards' src='cards/cardback.png' alt='back of card'></img>";
                    
                    
                    array_shift($dealersCardsArray);
                    
                    foreach($dealersCardsArray as $key=>$value){
                        
                        echo "<img class='cards' src=".'cards/'. $key . ".png alt=" .$key. "></img>";
                    }
                    
                    echo "</br>";
                    

                    return $sum;

                    //var_dump($deck);
                    //echo array_sum($dealersCardsArray);

                }
            }

            
            //blackjack yo
            while(count($deck) > 4 && $pmoney > 0){
                $ptotal = assignCards('player', $deck);
                $dtotal = assignCards('dealer', $deck);
                echo $ptotal . "<br>";
                echo $dtotal . "<br>";
               
                $round++;
                
               
                //echo 'c-before' . count($deck);
                
                //determining who wins by points and such
                if($ptotal == $dtotal || $ptotal > 21 && $dtotal > 21){
                    $roundwinner = "No One";
                }
                if($ptotal == 21 && $dtotal == 21){
                    $roundwinner = "Natural";
                }
                elseif($ptotal > $dtotal){
                    $pmoney -= $bet;
                    
                    if($ptotal > 21){
                        $roundwinner = "Dealer";
                    }
                    else{
                        if($ptotal == 21){
                            $blackjack++;
                        }
                        $pmoney += $winround;
                        $roundwinner = "$player";
                    }
                }   
                elseif($dtotal > $ptotal){
                    $pmoney -= $bet;
                    
                    if($dtotal > 21){
                        $pmoney += $winround;
                        $roundwinner = "$player";
                    }
                    else{
                        $roundwinner = "Dealer";
                    }
                }
                        
               // echo 'c-after' . count($deck);
            
                
                // v Still Just checking stuff
                //echo $ptotal . "<br/>";
                //echo $dtotal . "<br/>";
                
                //script of what to print after each game
                if($roundwinner !== "Natural"){
                    echo "
                    <p class='words1'>Round $round</p> 
                    <p class='words2'>Round Winner: $roundwinner</p> 
                    <p class='words2'>$players Money: $$pmoney</p>
                    <hr>
                    </br>
                    ";
                }
                else {
                    echo "
                    <p class='words1'>Round $round</p>
                    <p class='words2'>This round is a $roundwinner</p>
                    <p class='words2'>$players Money: $$pmoney</p>
                    <hr>
                    </br>
                    ";
                }
                
                //echo count($deck);
                // v Just checking stuff again
                //echo "Count" . count($deck) . "<br/>";
                if($pmoney == $original/2){
                    $halfway = true;
                    $amount = $round;
                }
                
                //script of what to print when a winner is found
                if(count($deck) <= 4 && $pmoney > 0 ){
                    if($halfway == true){
                      echo "The player hit half of what he started with in round: $amount <br/>";  
                    }
                    if($blackjack !== 0){
                        if($blackjack == 1){
                            echo "The player hit blackjack $blackjack time <br/>";
                        }
                        else{
                            echo "The player hit blackjack $blackjack times <br/>";
                        }
                    }
                    echo "Winner of the Game: $player! with $$pmoney";
                }
                elseif($pmoney == 0){
                    echo "Winner of the Game: Dealer! It took $round rounds to beat the player.";
                }
                
                echo $bet;
                echo " ";
                echo $winround;
            }
            
            
        ?>

    </p>

    </body>
</html>

