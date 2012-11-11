<?php
/**
 * BootstrapDatePicker.php
 *
 * @author  Jiří Šifalda <sifalda.jiri@gmail.com>
 * @package Flame
 *
 * @date    11.11.12
 */

namespace Flame\Forms\Controls;

class BootstrapDatePicker extends \Nette\Forms\Controls\BaseControl

{
	/** Range validator */
	const DATE_RANGE = ':dateRange';
	/** Default language */
	const DEFAULT_LANGUAGE = 'en';
	/** Defaut date format */
	const W3C_DATE_FORMAT = 'yyyy.mm.dd';
	/** Calendar viemode with month overview. */
	const STARTVIEW_MONTH = 0;
	/** Calendar viemode with year overview. */
	const STARTVIEW_YEAR = 1;
	/** Calendar viemode with dacade overview. */
	const STARTVIEW_DECADE = 2;
	/** Calendar without today button. */
	const TODAY_BUTTON_FALSE = false;
	/** Calendar with simple today button which scrolls calendar to now. */
	const TODAY_BUTTON_TRUE = true;
	/** Calendar with today button which scrolls calendar to now and set value. */
	const TODAY_BUTTON_LINKED = 'linked';
	/** Icon left */
	const BUTTON_STYLE_ICON_LEFT = 0;
	/** Icon right */
	const BUTTON_STYLE_ICON_RIGHT = 1;
	/** No icon */
	const BUTTON_STYLE_ICON_NONE = 2;
	/** @var     string            class name	Class to detect inputs in DOM */
	private $className = 'bootstrapDatePicker';

	/** @var     string						date format	Date format - d, m, M, y */
	private $format = self::W3C_DATE_FORMAT;

	/** @var     bool							autoclose flag	If true calendar will be closed on click */
	private $autoclose = false;

	/** @var     bool							If true today column will be highlighted */
	private $todayHighlight = false;

	/** @var     number						Definition of first day of week. 0 for Sunday, 6 for Saturday */
	private $weekStart = 1;

	/** @var     bool						Switch keyboard navigation on/off */
	private $keyboardNavigation = true;

	/** @var     number					Today button mode */
	private $todayButton = self::TODAY_BUTTON_FALSE;

	/** @var     number					Startview mode */
	private $startview = self::STARTVIEW_MONTH;

	/** @var     string					Language */
	private $language = self::DEFAULT_LANGUAGE;

	/** @var     number					Style */
	private $buttonStyle = self::BUTTON_STYLE_ICON_RIGHT;

	/** @var     string            value entered by user (unfiltered) */
	protected $rawValue;


	/**
	 * @param string $format
	 * @param string $language
	 * @param null $label
	 *
	 */
	public function __construct($format = self::W3C_DATE_FORMAT, $language=self::DEFAULT_LANGUAGE	, $label = null)
	{
		parent::__construct($label);
		$this->control->type = 'text';
		$this->format = $format;
		$this->language = $language;
	}

	/**
	 * @param string $format
	 * @param string $language
	 * @param string $method
	 */
	public static function register($format='yyyy.mm.dd', $language = 'en', $method = 'addDatePicker') {
		$class = function_exists('get_called_class')?get_called_class():__CLASS__;
		\Nette\Forms\Container::extensionMethod(
			$method, function (\Nette\Forms\Container $container, $name, $label = null) use ($class, $format, $language) {
				return $container[$name] = new $class($format, $language,  $label);
			}
		);
	}

	/**
	 * Switch keyboard navigation on / off
	 *
	 * @param bool $state
	 * @return BootstrapDatePicker
	 */
	public function setKeyboardNavigation($state = true)
	{
		$this->keyboardNavigation = $state;
		return $this;
	}

	/**
	 * Returns keyboard state
	 *
	 * @return   number
	 */
	public function getKeyboardNavigation()
	{
		return $this->keyboardNavigation;
	}

	/**
	 * Sets first day of week.
	 *
	 * @param    number 0 for sunday, 6 for staurday
	 * @return   self
	 */
	public function setWeekStart($weekStart)
	{
		$this->weekStart = $weekStart;
		return $this;
	}

	/**
	 * Returns first day of week for this calendar
	 *
	 * @return   number
	 */
	public function getWeekStart()
	{
		return $this->weekStart;
	}

	/**
	 * Sets startview mode
	 *
	 * @param    number 	STARTVIEW_MONTH | STARTVIEW_YEAR | STARTVIEW_DECADE
	 * @return   self
	 */
	public function setStartview($startview)
	{
		$this->startview = $startview;
		return $this;
	}

	/**
	 * Returns startview mode
	 *
	 * @return   number
	 */
	public function getStartview()
	{
		return $this->autoclose;
	}

	/**
	 * Returns today button mode.
	 *
	 * @return   number
	 */
	public function todayButton()
	{
		return $this->todayButton;
	}

	/**
	 * Sets today button mode
	 *
	 * @param string $todayButton
	 * @return BootstrapDatePicker
	 */
	public function setTodayButton($todayButton = self::TODAY_BUTTON_LINKED)
	{
		$this->todayButton = $todayButton;
		return $this;
	}
	/**
	 * Returns input button style.
	 *
	 * @return   number
	 */
	public function getInputButtonStyle()
	{
		return $this->buttonStyle;
	}

	/**
	 * Sets input button style
	 *
	 * @param int $buttonStyle
	 * @return BootstrapDatePicker
	 */
	public function setInputButtonStyle($buttonStyle = self::BUTTON_STYLE_ICON_RIGHT)
	{
		$this->buttonStyle = $buttonStyle;
		return $this;
	}
	
	/**
	 * Returns true if calendar should highlight today column.
	 *
	 * @return   bool
	 */
	public function isTodayHighlight()
	{
		return $this->todayHighlight;
	}

	/**
	 * Turns on today column highlighting
	 *
	 * @param    bool
	 * @return   self
	 */
	public function setTodayHighlight($highlight = true)
	{
		$this->todayHighlight = $highlight;
		return $this;
	}

	/**
	 * Returns true if calendar should be closed when date is selected.
	 *
	 * @return   bool
	 */
	public function isAutoclosed()
	{
		return $this->autoclose;
	}

	/**
	 * Sets class autoclose flag. When autoclose flag is set to true calendar will be closed when date is selected.
	 *
	 * @param    bool
	 * @return   self
	 */
	public function setAutoclose($autoclose = true)
	{
		$this->autoclose = $autoclose;
		return $this;
	}

	/**
	 * Returns class name.
	 *
	 * @return   string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * Sets class name for input element.
	 *
	 * @param    string
	 * @return   self
	 */
	public function setClassName($className)
	{
		$this->className = $className;
		return $this;
	}

	/**
	 * Generates control's HTML element.
	 *
	 * @return   \Nette\Web\Html
	 */
	public function getControl()
	{
		$control = parent::getControl();

		$outter = $this->buttonStyle == self::BUTTON_STYLE_ICON_LEFT||$this->buttonStyle == self::BUTTON_STYLE_ICON_RIGHT?\Nette\Utils\Html::el('div'):$control;
		if ($this->buttonStyle == self::BUTTON_STYLE_ICON_LEFT) {
			$outter->addClass('input-prepend date');
		} elseif($this->buttonStyle == self::BUTTON_STYLE_ICON_RIGHT) {
			$outter->addClass('input-append date');
		}
		$outter->addClass($this->className);
		$outter->data['date-autoclose'] = $this->autoclose?'true':'false';
		$outter->data['date-start-view'] = $this->startview;
		$outter->data['date-today-btn'] = $this->todayButton;
		$outter->data['date-today-highlight'] = $this->todayHighlight;
		$outter->data['date-weekstart'] = $this->weekStart;
		$outter->data['date-keyboard-navigation'] = $this->keyboardNavigation;
		$outter->data['date-format'] = $this->format;
		$outter->data['date-language'] = $this->language;

		list($min, $max) = $this->extractRangeRule($this->getRules());
		if ($min !== null) $outter->data['date-startdate'] = $min->format($this->toPhpFormat ($this->format));
		if ($max !== null) $outter->data['date-enddate'] = $max->format($this->toPhpFormat ($this->format));
		if ($this->value) $outter->data['date-date'] = $control->value = $this->value->format($this->toPhpFormat ($this->format));
		if ($this->buttonStyle == self::BUTTON_STYLE_ICON_LEFT) {
			$outter->add('<span class="add-on"><i class="icon-calendar"></i></span>');
			$outter->add($control);
		} elseif($this->buttonStyle == self::BUTTON_STYLE_ICON_RIGHT) {
			$outter->add($control);
			$outter->add('<span class="add-on"><i class="icon-calendar"></i></span>');
		}

		return $outter;
	}

	/**
	 * Sets DatePicker value.
	 *
	 * @param $value
	 * @return BootstrapDatePicker|\Nette\Forms\Controls\BaseControl
	 * @throws \InvalidArgumentException
	 */
	public function setValue($value)
	{
		if ($value instanceof \DateTime) {

		} elseif (is_int($value)) { // timestamp

		} elseif (empty($value)) {
			$rawValue = $value;
			$value = null;

		} elseif (is_string($value)) {
			$rawValue = $value;

			if (preg_match('#^(?P<dd>\d{1,2})[. -] *(?P<mm>\d{1,2})([. -] *(?P<yyyy>\d{4})?)?$#', $value, $matches)) {
				$dd = $matches['dd'];
				$mm = $matches['mm'];
				$yyyy = isset($matches['yyyy']) ? $matches['yyyy'] : date('Y');

				if (checkdate($mm, $dd, $yyyy)) {
					$value = "$yyyy-$mm-$dd";
				} else {
					$value = null;
				}
			}

		} else {
			throw new \InvalidArgumentException();
		}

		if ($value !== null) {
			// DateTime constructor throws Exception when invalid input given
			try {
				$value = \Nette\DateTime::from($value); // clone DateTime when given
			} catch (\Exception $e) {
				$value = null;
			}
		}

		if (!isset($rawValue) && isset($value)) {
			$rawValue = $value->format($this->toPhpFormat($this->format));
		}

		$this->value = $value;
		$this->rawValue = $rawValue;

		return $this;
	}



	/**
	 * Returns unfiltered value.
	 *
	 * @return   string
	 */
	public function getRawValue()
	{
		return $this->rawValue;
	}


	/**
	 * Does user enter anything? (the value doesn't have to be valid)
	 *
	 * @param \Nette\Forms\IControl $control
	 * @return bool
	 * @throws \InvalidStateException
	 */
	public static function validateFilled(\Nette\Forms\IControl $control)
	{
		if (!$control instanceof self) throw new \InvalidStateException('Unable to validate ' . get_class($control) . ' instance.');
		$rawValue = $control->rawValue;
		return !empty($rawValue);
	}


	/**
	 * Is entered value valid? (empty value is also valid!)
	 *
	 * @param \Nette\Forms\IControl $control
	 * @return bool
	 * @throws \InvalidStateException
	 */
	public static function validateValid(\Nette\Forms\IControl $control)
	{
		if (!$control instanceof self) throw new \InvalidStateException('Unable to validate ' . get_class($control) . ' instance.');
		$value = $control->value;
		return (empty($control->rawValue) || $value instanceof \DateTime);
	}

	/**
	 * Is entered values within allowed range?
	 *
	 * @param BootstrapDatePicker $control
	 * @param array $range
	 * @return bool
	 */
	public static function validateDateRange(self $control, array $range)
	{
		return ($range[0] === null || $control->getValue() >= $range[0]) && ($range[1] === null || $control->getValue() <= $range[1]);
	}

	/**
	 * Finds minimum and maximum allowed dates.
	 *
	 * @param \Nette\Forms\Rules $rules
	 * @return array
	 */
	private function extractRangeRule(\Nette\Forms\Rules $rules)
	{
		$controlMin = $controlMax = null;

		foreach ($rules as $rule) {
			if ($rule->type === \Nette\Forms\Rule::VALIDATOR) {
				if ($rule->operation === self::DATE_RANGE && !$rule->isNegative) {
					$ruleMinMax = $rule->arg;
				}

			} elseif ($rule->type === \Nette\Forms\Rule::CONDITION) {
				if ($rule->operation === \Nette\Forms\Form::FILLED && !$rule->isNegative && $rule->control === $this) {
					$ruleMinMax = $this->extractRangeRule($rule->subRules);
				}
			}

			if (isset($ruleMinMax)) {
				list($ruleMin, $ruleMax) = $ruleMinMax;
				if ($ruleMin !== null && ($controlMin === null || $ruleMin > $controlMin)) $controlMin = $ruleMin;
				if ($ruleMax !== null && ($controlMax === null || $ruleMax < $controlMax)) $controlMax = $ruleMax;
				$ruleMinMax = null;
			}
		}
		return array($controlMin, $controlMax);
	}

	/**
	 * Converts js date format string to php date() format string
	 * @param type $str
	 * @return type
	 */
	function toPhpFormat ($str) {
		$f = $this->strReplace(
			array('dd',	'd',	'mm',	'm',	'MM',	'M',	'yyyy',	'yyy',	'yy'),
			array('d',	'j',	'm',	'n',	'F',	'M',	'Y',		'y',		'y'),
			$str
		);
		return $f;
	}

	/**
	 * Explode string to array using delimiter the same way as php explode do but includes delimiter in array
	 * @param string $del
	 * @param string $str
	 * @return array
	 */
	private function strToArray ($del, $str) {
		$a = array();
		foreach(explode($del[1], $str) as $t) {
			$a[] = $t;
			$a[] = $del[0];
		}
		array_pop($a);
		return $a;
	}

	/**
	 * String replace function. Works similar to php str_replace(array, array, string) but stops replacing when pattern found
	 *
	 * @param type $a1
	 * @param type $a2
	 * @param type $str
	 * @return type
	 */
	private function strReplace($a1, $a2, $str) {
		$tokens = (array)$str;
		foreach($a1 as $del_i => $p) {
			foreach ($tokens as $tok_i => $token) {
				if (is_string($token)) {
					if (count($new=$this->strToArray(array($del_i, $p), $token))>1) {
						array_splice ($tokens, $tok_i, 1, $new);
						break;
					}
				}
			}
		}
		foreach($tokens as &$token) {
			if(is_int($token)) $token = $a2[$token];
		}
		return implode('', $tokens);
	}
}
