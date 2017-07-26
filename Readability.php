<?php
class Readability
{
    function ease_score($stringText)
    {
        # Calculate score
        $totalSentences = 1;
        $punctuationMarks = array('.', '!', ':', ';');
    
        foreach ($punctuationMarks as $punctuationMark) {
            $totalSentences += substr_count($stringText, $punctuationMark);
        }
    
        // get ASL value
        $totalWords = str_word_count($stringText);
        $ASL = $totalWords / $totalSentences;
    
        // find syllables value
        $syllableCount = 0;
        $arrWords = explode(' ', $stringText);
        $intWordCount = count($arrWords);
        //$intWordCount = $totalWords;
    
        for ($i = 0; $i < $intWordCount; $i++) {
            $syllableCount += $this->countSyllable($arrWords[$i]);
        }
    
        // get ASW value
        $ASW = $syllableCount / $totalWords;
    
        // Count the readability score
        //206.835 – (1.015 x ASL) – (84.6 x ASW)
        $score = 206.835 - (1.015 * $ASL) - (84.6 * $ASW);
        
        return $score;
    }
    
    function countSyllable($strWord) {
        $pattern = new Pattern();
        $strWord = trim($strWord);
    
        // Check for problem words
        if (isset($pattern->{'problem_words'}[$strWord])) {
            return $pattern->{'problem_words'}[$strWord];
        }
    
        // Check prefix, suffix
        $strWord = str_replace($pattern->{'prefix_and_suffix_patterns'}, '', $strWord, $tmpPrefixSuffixCount);
    
        // Removed non word characters from word
        $arrWordParts = preg_split('`[^aeiouy]+`', $strWord);
        $wordPartCount = 0;
        foreach ($arrWordParts as $strWordPart) {
            if ($strWordPart <> '') {
                $wordPartCount++;
            }
        }
        $intSyllableCount = $wordPartCount + $tmpPrefixSuffixCount;
    
        // Check syllable patterns 
        foreach ($pattern->{'subtract_syllable_patterns'} as $strSyllable) {
            $intSyllableCount -= preg_match('`' . $strSyllable . '`', $strWord);
        }
    
        foreach ($pattern->{'add_syllable_patterns'} as $strSyllable) {
            $intSyllableCount += preg_match('`' . $strSyllable . '`', $strWord);
        }
    
        $intSyllableCount = ($intSyllableCount == 0) ? 1 : $intSyllableCount;
        return $intSyllableCount;
    }

}

$readability = new Readability();
echo $readability->ease_score("Heavy metals are generally defined as metals with relatively high densities, atomic weights, or atomic numbers. The criteria used, and whether metalloids are included, vary depending on the author and context. In metallurgy, for example, a heavy metal may be defined on the basis of density, whereas in physics the distinguishing criterion might be atomic number, while a chemist would likely be more concerned with chemical behavior. More specific definitions have been published, but none of these have been widely accepted. The definitions surveyed in this article encompass up to 96 out of the 118 chemical elements; only mercury, lead and bismuth meet all of them.");
echo "\n";

$pattern = new Pattern();
echo "Printing size of the first of four pattern arrays: ";
echo sizeof($pattern->{'subtract_syllable_patterns'});

# What PHP version is this?
echo "\n";
echo 'Current PHP version: ' . phpversion();

?>
