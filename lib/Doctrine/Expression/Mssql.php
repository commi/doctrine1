<?php
/*
 *  $Id: Mssql.php 7490 2010-03-29 19:53:27Z jwage $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Doctrine_Expression_Mssql
 *
 * @package     Doctrine
 * @subpackage  Expression
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Expression_Mssql extends Doctrine_Expression_Driver
{
    /**
     * Return string to call a variable with the current timestamp inside an SQL statement
     * There are three special variables for current date and time:
     * - CURRENT_TIMESTAMP (date and time, TIMESTAMP type)
     * - CURRENT_DATE (date, DATE type)
     * - CURRENT_TIME (time, TIME type)
     *
     * @return string to call a variable with the current timestamp
     * @access public
     */
    public function now($type = 'timestamp')
    {
        switch ($type) {
            case 'time':
            case 'date':
            case 'timestamp':
            default:
                return 'GETDATE()';
        }
    }
    public function curdate()
    {
        return 'CAST(GETDATE() AS DATE)';
    }

    /**
     * return string to call a function to get a substring inside an SQL statement
     *
     * @return string to call a function to get a substring
     */
    public function substring($value, $position, $length = null)
    {
        if ( ! is_null($length)) {
            return 'SUBSTRING(' . $value . ', ' . $position . ', ' . $length . ')';
        }
        return 'SUBSTRING(' . $value . ', ' . $position . ', LEN(' . $value . ') - ' . $position . ' + 1)';
    }

    /**
     * locate
     * returns the position of the first occurrence of substring $substr in string $str
     *
     * @param string $substr literal string to find
     * @param string $str literal string
     *
     * @return integer
     */
    public function locate($str, $substr, $start = null)
    {
    	if(is_null($start))
        return 'CHARINDEX(CAST(' . $substr . ' AS NVARCHAR(max)), ' . $str . ')';
    	else
    		return 'CHARINDEX(CAST(' . $substr . ' AS NVARCHAR(max)), ' . $str . ', ' . $start . ')';
    }
    public function instr($str, $substr, $start = null)
    {
        return $this->locate($str, $substr, $start);
    }

    /**
     * Returns global unique identifier
     *
     * @return string to get global unique identifier
     */
    public function guid()
    {
        return 'NEWID()';
    }

    /**
     * Returns the length of a text field
     *
     * @param string $column
     *
     * @return string
     */
    public function length($column)
    {
        return 'LEN (' . $column . ')';
    }

    /**
     * Returns an integer representing the specified datepart of the specified date.
     *
     * datepart
     *
     * Is the parameter that specifies the part of the date to return. The table lists dateparts and abbreviations recognized by Microsoft¨ SQL Serverª.
     *
     * Datepart Abbreviations
     * year yy, yyyy
     * quarter qq, q
     * month mm, m
     * dayofyear dy, y
     * day dd, d
     * week wk, ww
     * weekday dw
     * hour hh
     * minute mi, n
     * second ss, s
     * millisecond ms
     *
     * @param $datepart
     * @param $date
     */
    public function date_part($datepart, $date)
    {
        // remove ' and " from datepart for dblib
        $datepart = str_replace(array('\'', '"'), '', $datepart);

        return 'DATEPART(' . $datepart . ', ' . $date . ')';
    }

    /**
     * Aliases IFNULL to ISNULL
     *
     * @param string $arg1
     * @param string $arg2
     *
     * @return string
     */
    public function ifnull()
    {
        $args = func_get_args();

        return 'ISNULL(' . implode(', ', $args) . ')';
    }


    public function trim($str)
    {
	    return 'LTRIM(RTRIM(' . $str . '))';
    }

	public function date_add($date, $intervalstr)
	{
		$m = [];
		preg_match('~INTERVAL\s+(.+)\s+([a-zA-Z]+)$~i', trim($intervalstr), $m);
		return "DATEADD(${m[2]}, ${m[1]}, $date)";
	}
}
