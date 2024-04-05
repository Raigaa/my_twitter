<?php


/**
 * Class Form
 * Permet de générer un formulaire rapidement et simplement 
 */
class Form
{

    /**
     * @var array Contient toutes les données transmisent en post
     *  
     */
    private $data;


    /**
     * @var string Tag utilisé pour entourer les champs
     */
    public $surround = 'p';


    /**
     * @param array $data
     * @param string $string
     * 
     */
    public function __construct($data = array())
    {

        $this->data = $data;

    }


    /**
     * @param $html string Code HTML à entourer
     * @return string
     */
    private function surround($html)
    {

        return "<{$this->surround}>{$html}</{$this->surround}>";

    }


    /**
     * @param string index de la valeur à récuperer
     * @return string
     */
    private function getValue($index)
    {

        return isset($this->data[$index]) ? $this->data[$index] : null;

    }


    /**
     * @param $type string
     * @param $name string
     * @return string
     */
    public function input($_name, $_type = 'text')
    {

        return $this->surround('<input type="'. $_type .'" name="' . $_name . '" value="' . $this->getValue($_name) . '">');

    }


    /**
     * @return string
     */
    public function submit()
    {

        return '<button type="submit">Envoyer</button>';

    }

   
    /**
    * Affiche juste les données transimise par la méthode POST
    */
    public function formData() 
    {
        foreach($this->data as $key => $value){
            if ($value != '') {
                if ($key === 'mdp') {
                    echo "$key: " . hash('SHA256',$value) . "<br>";   
                } else {
                    echo "$key: " . $value . "<br>";
                }
            }
        }
        // return isset($this->data) ? var_dump($this->data) : null;

    }


}


?>