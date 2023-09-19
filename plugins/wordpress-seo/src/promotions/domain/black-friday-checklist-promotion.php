<?php

namespace Yoast\WP\SEO\Promotions\Domain;

/**
 * Class to manage the Black Friday checklist promotion.
 *
 * @makePublic
 */
class Black_Friday_Checklist_Promotion extends Abstract_Promotion implements Promotion_Interface {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct(
			'black_friday_2023_checklist',
			new Time_Interval(
				\gmmktime( 11, 00, 00, 9, 19, 2023 ),
				\gmmktime( 11, 00, 00, 10, 31, 2023 )
			)
		);
	}
}
