<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 12/09/2017
 * Time: 23:05
 */

namespace components;


class contactForm extends component {

	protected $atts;
	protected $form;
	/**
	 * widget_class constructor.
	 */
	public function __construct($atts) {
		$this->atts=$atts;
	}

	/**
	 * @return string
	 */
	public function getForm(): string {
		if(!$this->form){
			$this->form=wpcf7_contact_form_tag_func(
				array(
					'id'=>180,
					'title'=>'contact'
				),
				'',
				'contact-form-7'
			);
		}
		return $this->form;
	}
}