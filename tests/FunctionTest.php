<?php

use PHPUnit\Framework\TestCase;

putenv("PRE_BASE_DIR=" . realpath(__DIR__ . "/../"));

class FunctionTest extends TestCase
{
    /**
     * @test
     */
    public function can_add_custom_macros()
    {
        Pre\Plugin\addMacro(__DIR__ . "/fixtures/find-replace.yay");

        $actual = Pre\Plugin\parse("<?php\n\nfind;\n");
        $expected = "<?php\n\nreplace;\n";

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @test
     */
    public function can_remove_custom_macros()
    {
        $path = __DIR__ . "/fixtures/find-replace.yay";

        Pre\Plugin\addMacro($path);
        Pre\Plugin\removeMacro($path);
        
        $actual = Pre\Plugin\parse("<?php\n\nfind;\n");
        $expected = "<?php\n\nfind;\n";

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * @test
     * @dataProvider builtInMacros
     */
    public function can_use_built_in_macros($from, $expected)
    {
        $actual = Pre\Plugin\parse($from);
        $this->assertEquals($expected, $actual);
    }

    public static function builtInMacros()
    {
        return [
            [
                "<?php\n\n..'/foo';\n",
                "<?php\n\n__DIR__ .'/foo';\n",
            ],
            [
                "<?php\n\nprocess ..'/foo';\n",
                "<?php\n\n\Pre\Plugin\process(__DIR__ . '/foo');\n",
            ],
            [
                "<?php\n\nprocess '/foo';\n",
                "<?php\n\n\Pre\Plugin\process('/foo');\n",
            ],
        ];
    }

    /**
     * @test
     */
    public function can_format_code()
    {
        $expected = "<?php\n\n\$func = function () {\n};\n";
        $actual = Pre\Plugin\format("<?php\n\n\$func = function\n()\n{\n};");

        $this->assertEquals($actual, $expected);
    }
}
