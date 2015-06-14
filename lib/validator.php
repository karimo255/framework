<?php

class Validator
{
    public static $regexes = Array(
    		'date' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
    		'amount' => "^[-]?[0-9]+\$",
    		'number' => "^[-]?[0-9,]+\$",
    		'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
    		'not_empty' => "[a-z0-9A-Z]+",
    		'words' => "^[A-Za-z]+[A-Za-z \\s]*\$",
    		'phone' => "^[0-9]{10,11}\$",
    		'zipcode' => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
    		'plate' => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
    		'price' => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
    		'2digitopt' => "^\d+(\,\d{2})?\$",
    		'2digitforce' => "^\d+\,\d\d\$",
    		'anything' => "^[\d\D]{1,}\$"
    );
    private $validations, $mandatories, $errors, $corrects, $fields;


    public function __construct($validations=array(), $mandatories = array())
    {
    	$this->validations = $validations;
    	$this->mandatories = $mandatories;
    	$this->errors = array();
    	$this->corrects = array();
    }


    public function validate($items)
    {
    	$this->fields = $items;
    	$havefailures = false;
    	foreach($items as $key=>$val)
    	{
    		if((strlen($val) == 0 || array_search($key, $this->validations) === false) && array_search($key, $this->mandatories) === false) 
    		{
    			$this->corrects[] = $key;
    			continue;
    		}
    		$result = self::validateItem($val, $this->validations[$key]);
    		if($result === false) {
    			$havefailures = true;
    			$this->addError($key, $this->validations[$key]);
    		}
    		else
    		{
    			$this->corrects[] = $key;
    		}
    	}

    	return(!$havefailures);
    }


    public function getScript($antwort) {
    	if(!empty($this->errors))
    	{
    		$errors = array();
    		foreach($this->errors as $key=>$val) { $errors[] = "'#{$key}[name={$key}]'"; }

    		$output = '$('.implode(',', $errors).').addClass("unvalidated");';	
    		$output .= "alert('Fehler im Formular');"; 
    	}
    	if(!empty($this->corrects) && empty($this->errors))
    	{
    		$corrects = array();
    		foreach($this->corrects as $key) { $corrects[] = "'#{$key}[name={$key}]'"; }
    		$output = '$('.implode(',', $corrects).').removeClass("unvalidated");';
    		$output .= " alert('$antwort');"; // or your nice validation here
                

    	}
    	$output = "<script type='text/javascript'>$(document).ready(function(){{$output}});</script>";
    	return($output);
    }



    private function addError($field, $type='string')
    {
    	$this->errors[$field] = $type;
    }



    public static function validateItem($var, $type)
    {
    	if(array_key_exists($type, self::$regexes))
    	{
    		$returnval =  filter_var($var, FILTER_VALIDATE_REGEXP, array("options"=> array("regexp"=>'!'.self::$regexes[$type].'!i'))) !== false;
    		return($returnval);
    	}
    	$filter = false;
    	switch($type)
    	{
    		case 'email':
    			$var = substr($var, 0, 254);
    			$filter = FILTER_VALIDATE_EMAIL;	
    		break;
    		case 'int':
    			$filter = FILTER_VALIDATE_INT;
    		break;
    		case 'boolean':
    			$filter = FILTER_VALIDATE_BOOLEAN;
    		break;
    		case 'ip':
    			$filter = FILTER_VALIDATE_IP;
    		break;
    		case 'url':
    			$filter = FILTER_VALIDATE_URL;
    		break;
    	}
    	return ($filter === false) ? false : filter_var($var, $filter) !== false ? true : false;
    }		



}