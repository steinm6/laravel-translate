<?php
namespace Steinm6\LaravelTranslate;

use Zend\I18n\Translator\Translator;
use Config;

class Translate
{
	protected $translate;
	protected $localePath;
	protected $locale;

	public function __construct()
	{
		$this->localePath = Config::get('translate.localePath');

		try {
			$this->translate = new Translator();
		} catch (\Exception $e) {
			dd($e);
		}

//      Language Setting is done via route filter...

//      $this->setLocale('en_US');

//		if (\Session::has($this->getSessionKey())) {
//			try {
//				$this->setLocale(\Session::get($this->getSessionKey()));
//			} catch (\Exception $e) {
//
//			}
//		}
	}

	/**
	 * Set the locale
	 *
	 * @param $locale
	 * @throws \Exception
	 */
	public function setLocale($locale)
	{
		if (strlen($locale) != 2) {
			$localeSingle = strtolower(substr($locale, 0, 2));
		} else {
			foreach (glob(app_path() . "/locale/" . $locale . "_*.mo") as $filename) {
				$localeSingle = $locale;
				$tmp          = explode("/", $filename);
				$locale       = str_replace('.mo', '', array_pop($tmp));
			}
		}

		if (!file_exists(app_path() . '/locale/' . $locale . '.mo')) {
			throw new \Exception("Language file not found: " . app_path() . '/locale/' . $locale . '.mo');
		}

		$this->translate->addTranslationFile('gettext', app_path() . '/locale/' . $locale . '.mo', '*', $localeSingle);
		$this->translate->setFallbackLocale($localeSingle);
		$this->translate->setLocale($localeSingle);
		$this->locale = $localeSingle;
	}

	/**
	 * Singular Translation
	 *
	 * @param $key
	 * @param array $replacements
	 * @return mixed|string
	 */
	public function _($key, array $replacements = array())
	{
		$translation = $this->translate->translate($key, '*', $this->locale);

		if (!empty($replacements)) {
			foreach ($replacements as $key => $value) {
				$translation = preg_replace('/\\:' . $key . '\b/u', $value, $translation);
			}
		}

		return $translation;
	}

	/**
	 * Plural Translation
	 *
	 * @param $key
	 * @param $pluralKey
	 * @param $count
	 * @param array $replacements
	 * @return mixed|string
	 */
	public function _n($key, $pluralKey, $count, array $replacements = array())
	{
		$translation = $this->translate->translatePlural($key, $pluralKey, $count, '*', $this->locale);

		if (!empty($replacements)) {
			foreach ($replacements as $key => $value) {
				$translation = preg_replace('/\\:' . $key . '\b/u', $value, $translation);
			}
		}

		return $translation;
	}

	/**
	 * Translates datetime
	 *
	 * @param $datetime
	 * @return string
	 */
	public function datetime($datetime)
	{
		$formatter = new \IntlDateFormatter($this->locale, \IntlDateFormatter::FULL, \IntlDateFormatter::FULL);

		return $formatter->format($datetime);
	}

	/**
	 * Translates currency
	 *
	 * @param $amount
	 * @return string
	 */
	public function currency($amount)
	{
		$formatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);

		return $formatter->format($amount);
	}

	/**
	 * Translates numbers
	 *
	 * @param $number
	 * @return string
	 */
	public function number($number)
	{
		$formatter = new \NumberFormatter($this->locale, \NumberFormatter::DECIMAL);

		return $formatter->format($number);
	}

} 