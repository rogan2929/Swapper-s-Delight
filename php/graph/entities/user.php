<?php

require_once 'graph-object.php';

/**
 * 
 * Graph API user object.
 * @author rogan2929
 *
 */
class User extends GraphObject {

	private $lastName;
	private $firstName;
	private $profileUrl;
	private $picSquare;

	public function getLastName() {
		return $this->lastName;
	}

	public function getFirstName() {
		return $this->firstName;
	}

	public function getProfileUrl() {
		return $this->profileUrl;
	}

	public function getPicSquare() {
		return $this->picSquare;
	}

	public function setLastName($lastName) {
		$this->lastName = $lastName;
	}

	public function setFirstName($firstName) {
		$this->firstName = $firstName;
	}

	public function setProfileUrl($profileUrl) {
		$this->profileUrl = $profileUrl;
	}

	public function setPicSquare($picSquare) {
		$this->picSquare = $picSquare;
	}

}