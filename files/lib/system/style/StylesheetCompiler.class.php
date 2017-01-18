<?php
namespace cms\system\style;

use cms\data\stylesheet\Stylesheet;
use Leafo\ScssPhp\Compiler;
use wcf\data\option\Option;
use wcf\system\style\StyleHandler;
use wcf\system\SingletonFactory;
use wcf\util\FileUtil;
use wcf\util\StringUtil;
use wcf\util\StyleUtil;

/**
 * Compiles stylesheets.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
class StylesheetCompiler extends SingletonFactory {
	/**
	 * less compiler object.
	 * @var	Compiler
	protected $compiler = null;

	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		require_once(WCF_DIR.'lib/system/style/scssphp/scss.inc.php');
		$this->compiler = new Compiler();
		$this->compiler->setImportPaths([WCF_DIR]);
	}

	/**
	 * Compiles LESS stylesheets.
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
		if (isset($variables['individualLess'])) {
			unset($variables['individualLess']);
		}

		// add style image path
		$imagePath = '../images/';
		if ($style->imagePath) {
			$imagePath = FileUtil::getRelativePath(WCF_DIR . 'style/', WCF_DIR . $style->imagePath);
			$imagePath = FileUtil::addTrailingSlash(FileUtil::unifyDirSeparator($imagePath));
		}
		$variables['style_image_path'] = "'{$imagePath}'";

		// apply overrides
		if (isset($variables['overrideLess'])) {
			$lines = explode("\n", StringUtil::unifyNewlines($variables['overrideLess']));
			foreach ($lines as $line) {
				if (preg_match('~^@([a-zA-Z]+): ?([@a-zA-Z0-9 ,\.\(\)\%\#-]+);$~', $line, $matches)) {
					$variables[$matches[1]] = $matches[2];
				}
			}
			unset($variables['overrideLess']);
		}

		// add options as LESS variables
		foreach (Option::getOptions() as $constantName => $option) {
			if (in_array($option->optionType, ['boolean', 'integer'])) {
				$variables['wcf_option_'.mb_strtolower($constantName)] = '~"'.$option->optionValue.'"';
			}
		}

		// compile
		$this->compiler->setVariables($variables);
		$content  = "/* stylesheet for '". $stylesheet->getTitle() ."', generated on ". gmdate('r') ." -- DO NOT EDIT */\n\n";
		if (!empty($stylesheet->scss)) {
			$content .= $this->compiler->compile($stylesheet->scss);
		} else {
			// backward compatibility to maelstrom & typhoon
			$content .= $this->compiler->compile($stylesheet->less);
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
}
