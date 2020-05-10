<?php
declare(strict_types=1);

namespace app\test;

use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    public function testDeprecationCanBeExpected() : void
    {
        $this->expectDeprecation();

        $this->expectDeprecationMessage("foo");

        $this->expectDeprecationMessageMatches('/foo/');

        trigger_error('foo', E_USER_DEPRECATED);
    }

    public function testNoticeCanBeExpected() : void
    {
        $this->expectNotice();

        $this->expectNoticeMessage('foo');

        $this->expectNoticeMessageMatches('/foo/');

        trigger_error('foo', E_USER_NOTICE);
    }

    public function testWarningCanBeExpeted() : void
    {
        $this->expectWarning();

        $this->expectWarningMessage('foo');

        $this->expectWarningMessageMatches('/foo/');

        trigger_error('foo', E_USER_WARNING);
    }

    public function testErrorCanBeExpected() : void
    {
        $this->expectError();

        $this->expectErrorMessage('foo');

        $this->expectErrorMessageMatches('/foo/');

        trigger_error('foo', E_USER_ERROR);
    }
}

