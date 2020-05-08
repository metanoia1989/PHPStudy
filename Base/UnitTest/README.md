# UnitTest 单元测试
PHPUnit文档：https://phpunit.readthedocs.io/en/9.1/index.html           


## 断言方法
* `assertArrayHasKey()`   断言数组键是否存在。反函数是 assertArrayNotHasKey() 
* `assertClassHasAttribute()`  断言类属性是否存在，反函数是 assertClassNotHasAttribute() 
* `assertClassHasStaticAttribute()` 断言类是否有静态属性，反函数是 assertClassNotHasStaticAttribute() 
* `assertContains()` 断言可迭代类型是否包含某元素，反函数是 assertNotContains()
* `assertStringContainsString()` 断言一个字符串是否是另一个的子串，反函数是 assertStringNotContainsString()
* `assertStringContainsStringIgnoringCase()` 同上但是忽略大小写，反函数是 assertStringNotContainsStringIgnoringCase()
* `assertContainsOnly()` 断言可迭代类型是否仅包含指定类型的元素，反函数是 assertNotContainsOnly()
* `assertContainsOnlyInstancesOf()` 断言可迭代类型是否仅包含指定类实例的元素
* `assertCount()` 断言可迭代类型的元素数量，反函数是 assertNotCount() 
* `assertDirectoryExists()` 断言指定目录存在，反函数是 assertDirectoryDoesNotExist()
* `assertDirectoryIsReadable()` 断言指定目录是可读的，反函数是 assertDirectoryIsNotReadable()
* `assertDirectoryIsWritable()` 断言指定目录是可写的，反函数是 assertDirectoryIsNotWritable()
* `assertEmpty()` 断言元素为空，反函数为 assertNotEmpty()
* `assertEquals()` 断言两个变量相等，反函数为 assertNotEquals()
* `assertEqualsCanonicalizing()` 断言两个变量是否相等，比较数组时会先排序，比较对象时会将对象属性转换为数组，反函数是 assertNotEqualsCanonicalizing()
* `assertEqualsIgnoringCase()` 断言两个变量是否相等，忽略大小写，反函数是 assertNotEqualsIgnoringCase()
* `assertFalse()` 断言元素为false，反函数为 assertNotFalse()
* `assertFileEquals()` 断言两个文件的内容一样，反函数是 assertFileNotEquals()
* `assertFileExists()` 断言文件是否存在，反函数是 assertFileDoesNotExist()
* `assertFileIsReadable()` 断言文件是否是可读的，反函数是 assertFileIsNotReadable() 
* `assertFileIsWritable()` 断言文件是否是可写的，反函数是 assertFileIsNotWritable() 
* `assertGreaterThan()` 断言后面的值大于前面的，assertAttributeGreaterThan()比较类或对象的属性
* `assertGreaterThanOrEqual()` 断言后面的值大于或等于前面的，assertAttributeGreaterThanOrEqual()比较类或对象的属性
* `assertInfinite()` 断言值是否是无限大，反函数是 assertFinite()
* `assertInstanceOf()` 断言是否是类的实例对象，反函数是 assertNotInstanceOf()
* `assertIsArray()` 断言是否是数组，反函数是 assertIsNotArray()
* `assertIsBool()` 断言类型是否是布尔值，反函数是 assertIsNotBool()
* `assertIsCallable()` 断言值是否是可调用的，反函数是 assertIsNotCallable()
* `assertIsFloat()` 断言是否是浮点数，反函数是 assertIsNotFloat()
* `assertIsInt()` 断言是否是整数，反函数是 assertIsNotInt()
* `assertIsIterable()` 断言是否是可迭代的，反函数是 assertIsNotIterable() 
* `assertIsNumeric()` 断言是否是数值类型，反函数是 assertIsNotNumeric()
* `assertIsObject()` 断言是否是对象，反函数是 assertIsNotObject()
* `assertIsResource()` 断言是否是资源类型，反函数是 assertIsNotResource()
* `assertIsScalar()` 断言是否是标量类型，反函数是 assertIsNotScalar()
* `assertIsString()` 断言是否是字符串类型，反函数是 assertIsNotString()
* `assertIsReadable()` 断言文件或目录是否是可读的，反函数是 assertIsNotReadable()
* `assertIsWritable()` 断言文件和目录是否是可写的，反函数是 assertIsNotWritable()
* `assertJsonFileEqualsJsonFile()` 断言两个json文件的值是否匹配
* `assertJsonStringEqualsJsonFile()` 断言json字符串和json文件的内容是否匹配
* `assertJsonStringEqualsJsonString()` 断言两个json字符串的值是否匹配
* `assertLessThan()` 断言后一个参数是否小于前一个
* `assertLessThanOrEqual()` 断言后一个参数是否小于或等于前一个
* `assertNan()` 断言值是否是 NAN
* `assertNull()` 断言变量是否是null，反函数是 assertNotNull()
* `assertObjectHasAttribute()` 断言对象是否有某个属性，反函数是 assertObjectNotHasAttribute()
* `assertMatchesRegularExpression()` 断言正则是否匹配字符串，反函数是 assertDoesNotMatchRegularExpression()
* `assertStringMatchesFormat()` 断言字符串是否匹配格式化字符，反函数是 assertStringNotMatchesFormat()
* `assertStringMatchesFormatFile()` 断言字符串是否匹配文件的内容，反函数是 assertStringNotMatchesFormatFile()
* `assertSame()` 断言两个变量是否有同样的类型和值，反函数是 assertNotSame()
* `assertStringEndsWith()` 断言一个字符串是否是另一个字符串的结尾，反函数是 assertStringEndsNotWith()
* `assertStringEqualsFile()` 断言文件是否有该字符串内容，反函数是 assertStringNotEqualsFile()
* `assertStringStartsWith()` 断言一个字符串是否是另一个字符串的开头，反函数是 assertStringStartsNotWith()
* `assertThat()` 使用`PHPUnit\Framework\Constraint`类来表达更复杂的断言。
* `assertTrue()` 断言值是否为 true，反函数是 assertNotTrue()
* `assertXmlFileEqualsXmlFile()` 断言两个xml文件的内容是否相等，反函数是 assertXmlFileNotEqualsXmlFile()
* `assertXmlStringEqualsXmlFile()` 断言xml字符串是否与xml文件内容相等，反函数是 assertXmlStringNotEqualsXmlFile()
* `assertXmlStringEqualsXmlString()` 断言两个xml字符串是否相等，反函数是 assertXmlStringNotEqualsXmlString()


## Annotations 注解
注释是一种特殊形式的语法元数据，可以添加到某些编程语言的源代码中。 尽管PHP没有用于注释源代码的专用语言功能，但是在PHP社区中已经建立了在文档块中使用诸如`@annotation 参数`之类的标记来注释源代码的用法。 在PHP中，文档块具有反射性：可以通过反射API的`getDocComment()`方法在函数，类，方法和属性级别上对其进行访问。 诸如PHPUnit之类的应用程序在运行时使用此信息来配置其行为。

本附录展示了PHPUnit支持的各种注释。

* `@author` @group 的别名，允许根据作者过滤测试。
* `@after` 用于指明此方法应当在测试用例类中的每个测试方法运行完成之后调用。
* `@afterClass` 用于指明此静态方法应该于测试类中的所有测试方法都运行完成之后调用，用于清理共享基境。
* `@backupGlobals` 全局变量的备份与还原操作可以对某个测试用例类中的所有测试彻底禁用，可以用在测试方法这一级别。这样可以对备份与还原操作进行更细粒度的配置。
* `@backupStaticAttributes` 如果指定了 @backupStaticAttributes 标注，那么将在每个测试之前备份所有已声明的类的静态属性的值，并在测试完成之后全部恢复。它可以用在测试用例类或测试方法级别。
* `@before` 用于指明此方法应当在测试用例类中的每个测试方法开始运行之前调用。
* `@beforeClass`用于指明此静态方法应该于测试类中的所有测试方法都运行完成之前调用，用于建立共享基境。
* `@codeCoverageIgnore` @codeCoverageIgnore, @codeCoverageIgnoreStart and @codeCoverageIgnoreEnd 标注用于从覆盖率分析中排除掉某些代码行。
* `@covers` 指明测试方法想要对哪些方法进行测试，如果提供了此标注，则代码覆盖率信息中只考虑指定的这些方法。
* `@coversDefaultClass` 用于指定一个默认的命名空间或类名，这样就不用在每个 @covers 标注中重复长名称。
* `@coversNothing` 指明所标注的测试用例不需要记录任何代码覆盖率信息。
* `@dataProvider` 测试方法可以接受任意参数。这些参数可以由数据供给器方法提供。所要使用的数据供给器方法用 @dataProvider 标注来指定。
* `@depends` 表达测试方法之间的依赖关系。
* `@doesNotPerformAssertions` 防止不执行任何断言的测试被认为是有风险的。
* `@group` 标记为属于一个或多个组。测试可以基于组来选择性的执行，使用命令行测试执行器的 `--group` and `--exclude-group` 选项，或者使用对应的 XML 配置文件指令。
* `@large`  @group large 的别名。如果安装了 PHP_Invoker 组件包并启用了严格模式，一个执行时间超过60秒的大型(large)测试将视为失败。这个超时限制可以通过 XML 配置文件的 timeoutForLargeTests 属性进行配置。
* `@medium` @group medium 的别名。中型(medium)测试不能依赖于标记为 @large 的测试。如果安装了 PHP_Invoker 组件包并启用了严格模式，一个执行时间超过10秒的中型(medium)测试将视为失败。这个超时限制可以通过 XML 配置文件的 timeoutForMediumTests 属性进行配置。
* `@preserveGlobalState` 在单独的进程中运行测试时，PHPUnit 会尝试保持来自父进程的全局状态（通过在父进程序列化全局状态然后在子进程反序列化的方式）。这当父进程包含非可序列化的全局内容时可能会导致问题。为了修正这种问题，可以用 @preserveGlobalState 标注来禁止 PHPUnit 保持全局状态。
* `@requires` 用于在常规前提条件（例如 PHP 版本或所安装的扩展）不满足时跳过测试。
* `@runTestsInSeparateProcesses` 指明单个测试类内的所有测试要各自运行在独立的 PHP 进程中。
* `@runInSeparateProcess` 指明某个测试要运行在独立的 PHP 进程中。
* `@small`  @group small 的别名。小型(small)测试不能依赖于标记为 @medium 或 @large 的测试。如果安装了 PHP_Invoker 组件包并启用了严格模式，一个执行时间超过1秒的小型(small)测试将会视为失败。这个超时限制可以通过 XML 配置文件的 timeoutForSmallTests 属性进行配置。
* `@test` 除了用 test 作为测试方法名称的前缀外，还可以在方法的文档注释块中用 @test 标注来将其标记为测试方法。
* `@testdox` 指定在生成敏捷文档语句时使用的替代描述。@testdox注释可以应用于测试类和测试方法。在方法级别将@testdox注释与@dataProvider一起使用时，可以在替代描述中将方法参数用作占位符。
* `@testWith` 您可以使用@testWith注释定义数据集，而不是实现与@dataProvider一起使用的方法。一个数据集由一个或多个元素组成。若要定义具有多个元素的数据集，请在单独的行中定义每个元素。数据集的每个元素必须是JSON中定义的数组。
* `@ticket` @group注释的别名，允许根据它们的ticket ID过滤测试。
* `@uses` 用来指明那些将会在测试中执行到但同时又不打算让其被测试所覆盖的代码。