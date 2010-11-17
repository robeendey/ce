<?php

class Install_Import_Ning_UserFields extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_user_fields_values';

  protected function  _translateRow(array $data, $key = null)
  {
    if( empty($data['profileQuestions']) ) {
      return false;
    }

    $userIdentity =  $this->getUserMap($data['contributorName']);

    // Get main data
    if( strpos($data['fullName'], ' ') !== false ) {
      list($firstName, $lastName) = @explode(' ', $data['fullName']);
      $questions['first name'] = $firstName;
      $questions['last name'] = $lastName;
    } else {
      $questions['first name'] = $data['fullName'];
    }
    if( @$data['gender'] == 'm' ) {
      $questions['gender'] = 2;
    } else if( @$data['gender'] == 'f' ) {
      $questions['gender'] = 3;
    }

    $questions['birthdate'] = @$data['birthdate'];

    //$questions['location'] = $data['location'];
    //$questions['country'] = $data['country'];

    // Get other data
    $questions = array_merge($questions, $data['profileQuestions']);
    $questions = array_filter($questions);

    // Decode all labels and ucwords
    $fixedQuestions = array();
    foreach( $questions as $question => $answer ) {
      $fixedQuestions[$question] = ucwords(htmlspecialchars_decode($question, ENT_QUOTES));
    }

    $values = array(
      '1' => '1',
    );

    foreach( $fixedQuestions as $question => $fixedQuestion ) {
      // Get field
      $field = $this->getToDb()->select()
        ->from('engine4_user_fields_meta')
        ->where('label LIKE ?', substr($fixedQuestion, 0, 63) . '%')
        ->limit(1)
        ->query()
        ->fetch();

      // Create
      if( !$field ) {
        // Create field
        $type = ( strlen($questions[$question]) > 100 ? 'textarea' : 'text' );
        $field = array(
          'type' => $type,
          'label' => $fixedQuestion,
          'display' => 1,
          'order' => 9999,
        );
        $this->getToDb()->insert('engine4_user_fields_meta', $field);
        $field['field_id'] = $this->getToDb()->lastInsertId();
        // Create map
        $this->getToDb()->insert('engine4_user_fields_maps', array(
          'field_id' => 1,
          'option_id' => 1,
          'child_id' => $field['field_id'],
          'order' => 9999,
        ));
      }
      $field_id = $field['field_id'];

      $values[$field_id] = $questions[$question];
    }

    foreach( $values as $field_id => $value ) {
      $this->getToDb()->insert('engine4_user_fields_values', array(
        'item_id' => $userIdentity,
        'field_id' => $field_id,
        'value' => $value,
      ));
    }

    return false;
  }
}