<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SemanticVersionChecker\Test\Unit\Filter\AllowedChangeFilter\PhpIgnoredTagFilterTest;

class RemovedNonIgnoredTagValues
{
    /**
     * This tag is one whose values are not ignored and thus needs to be compared
     * After removing the tag values, the files do not match
     * @nonignoredvals
     */
    public function foo()
    {
        return;
    }
}
