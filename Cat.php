<?php

class Cat{
    public $id,$name,$breed,$sex,$age,$colour;
    

    public function set($array) {
            if (!empty($array['id'])) {
                $this->id = $array['id'];
            }
            $this ->name = $array['name'];
            $this->breed = $array['breed'];
            $this->sex = $array['sex'];
            $this->age = $array['age'];
            $this->colour = $array['colour'];
    }
}

?>