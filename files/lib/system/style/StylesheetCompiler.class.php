<?php
namespace cms\system\style;

use cms\data\stylesheet\Stylesheet;
use Leafo\ScssPhp\Compiler;
use wcf\data\option\Option;
use wcf\system\exception\SystemException;
use wcf\system\style\StyleHandler;
use wcf\system\SingletonFactory;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\util\StyleUtil;

/**
 * Compiles stylesheets.
 *
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2017 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetCompiler extends SingletonFactory {
	/**
	 * @var Compiler
	 */
	protected $compiler = null;

	/**
	 * @inheritDoc
	 */
	public static $supportedOptionType = ['boolean', 'integer'];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		require_once(WCF_DIR.'lib/system/style/scssphp/scss.inc.php');
		$this->compiler = new Compiler();
		$this->compiler->setImportPaths([WCF_DIR]);
		$this->compiler->setFormatter('Leafo\ScssPhp\Formatter\Crunched');
	}

	/**
	 * Compiles SCSS stylesheets.
	 *
	 * @param	\cms\data\stylesheet\Stylesheet		$stylesheet
	 * @param	integer					$styleID
	 */
	public function compile(Stylesheet $stylesheet, $styleID = null) {
		$styles = StyleHandler::getInstance()->getStyles();

		// compile stylesheet for all installed styles
		if ($styleID === null) {
			foreach ($styles as $style) {
				$this->compile($stylesheet, $style->styleID);
			}
			return;
		}

		$style = $styles[$styleID];

		// get style variables
		$variables = $style->getVariables();
		if (isset($variables['individualScss'])) {
			unset($variables['individualScss']);
		}

		// add style image path
		$imagePath = '../images/';
		if ($style->imagePath) {
			$imagePath = FileUtil::getRelativePath(WCF_DIR . 'style/', WCF_DIR . $style->imagePath);
			$imagePath = FileUtil::addTrailingSlash(FileUtil::unifyDirSeparator($imagePath));
		}
		$variables['style_image_path'] = "'{$imagePath}'";

		// apply overrides
		if (isset($variables['overrideScss'])) {
			$lines = explode("\n", StringUtil::unifyNewlines($variables['overrideScss']));
			foreach ($lines as $line) {
				if (preg_match('~^@([a-zA-Z]+): ?([@a-zA-Z0-9 ,\.\(\)\%\#-]+);$~', $line, $matches)) {
					$variables[$matches[1]] = $matches[2];
				}
			}
			unset($variables['overrideScss']);
		}

		// add options as SCSS variables
		if (PACKAGE_ID) {
			foreach (Option::getOptions() as $constantName => $option) {
				if (in_array($option->optionType, static::$supportedOptionType)) {
					$variables['wcf_option_'.mb_strtolower($constantName)] = is_int($option->optionValue) ? $option->optionValue : '"'.$option->optionValue.'"';
				}
			}
		}
		else {
			// workaround during setup
			$variables['wcf_option_attachment_thumbnail_height'] = '~"210"';
			$variables['wcf_option_attachment_thumbnail_width'] = '~"280"';
			$variables['wcf_option_signature_max_image_height'] = '~"150"';
		}

		$content  = "/* stylesheet for '". $stylesheet->getTitle() ."', generated on ". gmdate('r') ." -- DO NOT EDIT */\n\n";

		// build SCSS bootstrap
		$scss = $this->bootstrap($variables);
		$scss .= $stylesheet->scss;

		// compile
		$this->compiler->setVariables($variables);
		if (!empty($stylesheet->scss)) {
			$content .= $this->compiler->compile($scss);
		}

		// compress stylesheet
		$lines = explode("\n", $content);
		$content = $lines[0] . "\n" . $lines[1] . "\n";
		for ($i = 2, $length = count($lines); $i < $length; $i++) {
			$line = trim($lines[$i]);
			$content .= $line;

			switch (substr($line, -1)) {
				case ',':
					$content .= ' ';
					break;

				case '}':
					$content .= "\n";
					break;
			}

			if (substr($line, 0, 6) == '@media') {
				$content .= "\n";
			}
		}

		// write stylesheet
		$filename = $stylesheet->getLocation($styleID);
		file_put_contents($filename, $content);
		FileUtil::makeWritable($filename);

		// write rtl stylesheet
		$content = StyleUtil::convertCSSToRTL($content);
		$filename = $stylesheet->getLocation($styleID, true);
		file_put_contents($filename, $content);
		FileUtil::makeWritable($filename);
	}

	/**
	 * @inheritDoc
	 */
	protected function bootstrap(array $variables) {
		$content = '';
		
		// add reset like a boss
		//$content = $this->prepareFile(WCF_DIR.'style/bootstrap/reset.scss');

		// apply style variables
		$this->compiler->setVariables($variables);

		// add mixins
		$content .= $this->prepareFile(WCF_DIR.'style/bootstrap/mixin.scss');

		// add newer mixins added with version 3.0
		foreach (glob(WCF_DIR.'style/bootstrap/mixin/*.scss') as $mixin) {
			$content .= $this->prepareFile($mixin);
		}

		return $content;
	}

	/**
	 * @inheritDoc
	 */
	protected function prepareFile($filename) {
		if (!file_exists($filename) || !is_readable($filename)) {
			throw new SystemException("Unable to access '".$filename."', does not exist or is not readable");
		}

		// use a relative path
		$filename = FileUtil::getRelativePath(WCF_DIR, dirname($filename)) . basename($filename);
		return '@import "'.$filename.'";'."\n";
	}
}
