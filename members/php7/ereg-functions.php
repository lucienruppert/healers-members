<?php
//php7 hiányzó funkciói

	if (function_exists('ereg') !== true) {
		function ereg($pattern, $string, &$regs) {
			return preg_match('~' . addcslashes($pattern, '~') . '~', $string, $regs);
		}
	}

	if (function_exists('eregi') !== true) {
		function eregi($pattern, $string, &$regs) {
			return preg_match('~' . addcslashes($pattern, '~') . '~i', $string, $regs);
		}
	}

	if (function_exists('ereg_replace') !== true) {
		function ereg_replace($pattern, $string, $replace) {
			return preg_replace('~' . addcslashes($pattern, '~') . '~', $string, $replace);
		}
	}

	if (function_exists('eregi_replace') !== true) {
		function eregi_replace($pattern, $string, $replace) {
			return preg_replace('~' . addcslashes($pattern, '~') . '~i', $string, $replace);
		}
	}

	if (function_exists('split') !== true) {
		function split($pattern, $string, $limit=-1) {
			return preg_split('~' . addcslashes($pattern, '~') . '~', $string, $limit);
		}
	}

	if (function_exists('spliti') !== true) {
		function spliti($pattern, $string, $limit=-1) {
			return preg_split('~' . addcslashes($pattern, '~') . '~i', $string, $limit);
		}
	}

	if (function_exists('sql_regcase') !== true) {
		function sql_regcase($string) {
			$out = '';
			for ($i=0; $i<strlen($string); $i++) {
				$char = $string[$i];
				$up = strtoupper($char);
				$low = strtolower($char);
				if ($up != $low) {
					$out .= "[${up}${low}]";
				} else {
					$out .= $char;
				}
			}
			return $out;
		}
	}
