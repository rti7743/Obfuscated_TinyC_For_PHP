/*
最小のCコンパイラのPHP移植です。
まだ動きません。

オリジナル:
http://bellard.org/otcc/
*/
/*
  Obfuscated Tiny C Compiler with ELF output

  Copyright (C) 2001-2003 Fabrice Bellard

  This software is provided 'as-is', without any express or implied
  warranty.  In no event will the authors be held liable for any damages
  arising from the use of this software.

  Permission is granted to anyone to use this software for any purpose,
  including commercial applications, and to alter it and redistribute it
  freely, subject to the following restrictions:

  1. The origin of this software must not be misrepresented; you must not
     claim that you wrote the original software. If you use this software
     in a product, an acknowledgment in the product and its documentation 
     *is* required.
  2. Altered source versions must be plainly marked as such, and must not be
     misrepresented as being the original software.
  3. This notice may not be removed or altered from any source distribution.
*/
//#ifndef TINY
//#include <stdarg.h>
//#endif
//#include <stdio.h>

/* vars: value of variables 
   loc : local variable index
   glo : global variable ptr
   data: base of data segment
   ind : output code ptr
   prog: output code
   rsym: return symbol
   sym_stk: symbol stack
   dstk: symbol stack pointer
   dptr, dch: macro state

   * 'vars' format: 
   For each character TAG_TOK at offset 'i' before a
    symbol in sym_stk, we have:
    v = (int *)(vars + 8 * i + TOK_IDENT)[0] 
    p = (int *)(vars + 8 * i + TOK_IDENT)[0] 
    
    v = 0    : undefined symbol, p = list of use points.
    v = 1    : define symbol, p = pointer to define text.
    v < LOCAL: offset on stack, p = 0.
    otherwise: symbol with value 'v', p = list of use points.

   * 'sym_stk' format:
   TAG_TOK sym1 TAG_TOK sym2 .... symN '\0'
   'dstk' points to the last '\0'.
*/
class TinyC
{
//int tok, tokc, tokl, ch, vars, rsym, prog, ind, loc, glo, file, sym_stk, dstk, dptr, dch, last_id, data, text, data_offset;
	var $tok, $tokc, $tokl, $ch, $vars, $rsym, $prog, $ind, $loc, $glo, $file, $sym_stk, $dstk, $dptr, $dch, $last_id, $data, $text, $data_offset;

//#define ALLOC_SIZE 99999
	const ALLOC_SIZE = 99999;

//#define ELFOUT

/* depends on the init string */
//#define TOK_STR_SIZE 48
	const TOK_STR_SIZE     = 48;
//#define TOK_IDENT    0x100
	const TOK_IDENT     = 0x100;
//#define TOK_INT      0x100
	const TOK_INT     = 0x100;
//#define TOK_IF       0x120
	const TOK_IF     = 0x120;
//#define TOK_ELSE     0x138
	const TOK_ELSE     = 0x138;
//#define TOK_WHILE    0x160
	const TOK_WHILE     = 0x160;
//#define TOK_BREAK    0x190
	const TOK_BREAK     = 0x190;
//#define TOK_RETURN   0x1c0
	const TOK_RETURN     = 0x1c0;
//#define TOK_FOR      0x1f8
	const TOK_FOR     = 0x1f8;
//#define TOK_DEFINE   0x218
	const TOK_DEFINE     = 0x218;
//#define TOK_MAIN     0x250
	const TOK_MAIN     = 0x250;

//#define TOK_DUMMY   1
	const	TOK_DUMMY   = 1;
//#define TOK_NUM     2
	const	TOK_NUM   = 2;

//#define LOCAL   0x200
	const LOCAL   = 0x200

//#define SYM_FORWARD 0
	const SYM_FORWARD = 0;
//#define SYM_DEFINE  1
	const SYM_DEFINE  = 1;

/* tokens in string heap */
//#define TAG_TOK    ' '
	const TAG_TOK    = ' '
//#define TAG_MACRO  2
	const TAG_MACRO    = 2
//#define TAG_MACRO  2

/* additionnal elf output defines */
//#ifdef ELFOUT

//#define ELF_BASE      0x08048000
	const ELF_BASE      = 0x08048000;
//#define PHDR_OFFSET   0x30
	const PHDR_OFFSET   = 0x30;

//#define INTERP_OFFSET 0x90
	const INTERP_OFFSET = 0x90;
//#define INTERP_SIZE   0x13
	const INTERP_SIZE = 0x13;

//#ifndef TINY
//#define DYNAMIC_OFFSET (INTERP_OFFSET + INTERP_SIZE + 1)
	const DYNAMIC_OFFSET = (INTERP_OFFSET + INTERP_SIZE + 1);
#define DYNAMIC_SIZE   (11*8)
	const DYNAMIC_SIZE = (11*8);

//#define ELFSTART_SIZE  (DYNAMIC_OFFSET + DYNAMIC_SIZE)
	const ELFSTART_SIZE = (DYNAMIC_OFFSET + DYNAMIC_SIZE);
//#else
//#define DYNAMIC_OFFSET 0xa4
//#define DYNAMIC_SIZE   0x58
//
//#define ELFSTART_SIZE  0xfc
//#endif

/* size of startup code */
//#define STARTUP_SIZE   17
	const STARTUP_SIZE   = 17;

/* size of library names at the start of the .dynstr section */
//#define DYNSTR_BASE      22
	const DYNSTR_BASE      = 22;

//#endif

//pdef(t)
function pdef($t)
//{
{
//    *(char *)dstk++ = t;
	$this->stackMemory[$this->dstk++] = t;
//}
}

//int inp()
function inp()
//{
{
//    if (dptr) {
	if ($this->dptr) {
//        ch = *(char *)dptr++;
		$this->ch = $this->tokMemory[$this->dptr++];
//        if (ch == TAG_MACRO) {
        if ($this->ch == self::TAG_MACRO) {
//            dptr = 0;
            $this->dptr = 0;
//            ch = dch;
            $this->ch = $this->dch;
//        }
        }
//    } else
    } else
//        ch = fgetc(file);
        $this->ch = fgetc($this->file);
    /*    printf("ch=%c 0x%x\n", ch, ch); */
    printf("ch=%c 0x%x\n", $this->ch, $this->ch);
//}
}

//int isid()
function isid()
//{
{
//    return isalnum(ch) | ch == '_';
    return isalnum($this->ch) | $this->ch == '_';
//}
}

/* read a character constant */
//getq()
function getq()
//{
{
//    if (ch == '\\') {
    if ($this->ch == '\\') {
//        inp();
        $this->inp();
//        if (ch == 'n')
        if ($this->ch == 'n')
//            ch = '\n';
            $this->ch = '\n';
//    }
    }
//}
}

//next()
function next()
//{
{
//    int t, l, a;
	$t = 0;    $l = 0;    $a = 0;

//    while (isspace(ch) | ch == '#') {
	while (isspace($this->ch) | $this->ch == '#') {
//        if (ch == '#') {
		if ($this->ch == '#') {
//            inp();
			$this->inp();
//            next();
			$this->next();
//            if (tok == TOK_DEFINE) {
			if ($this->tok == TOK_DEFINE) {
//                next();
				$this->next();
//                pdef(TAG_TOK); /* fill last ident tag */
				$this->pdef(self::TAG_TOK); /* fill last ident tag */
//                *(int *)tok = SYM_DEFINE;
				$this->tokMemory[$this->tok] = SYM_DEFINE;
//                *(int *)(tok + sizeof(int*)) = dstk; /* define stack */
				$this->tokmemory[$this->tok + self::POINTERSIZE] = $this->dstk;
//            }
			}
            /* well we always save the values ! */
//            while (ch != '\n') {
			while ($this->ch != '\n') {
//                pdef(ch);
				$this->pdef($this->ch);
//                inp();
				$this->inp();
//            }
			}
//            pdef(ch);
			$this->pdef(ch);
//            pdef(TAG_MACRO);
			$this->pdef(TAG_MACRO);
//        }
		}
//        inp();
		$this->inp();
//    }
	}
//    tokl = 0;
	$this->tokl = 0;
//    tok = ch;
	$this->tok = ch;
    /* encode identifiers & numbers */
//    if (isid()) {
	if ($this->isid()) {
//        pdef(TAG_TOK);
		$this->pdef(self::TAG_TOK);
//        last_id = dstk;
		$this->last_id = $this->dstk;
//        while (isid()) {
		while ($this->isid()) {
//            pdef(ch);
			$this->pdef($this->ch);
//            inp();
			$this->inp();
//        }
		}
//        if (isdigit(tok)) {
		if (isdigit($this->tok)) {
//            tokc = strtol(last_id, 0, 0);
			$this->tokc = strtol($this->last_id, 0, 0);
//            tok = TOK_NUM;
			$this->tok = self::TOK_NUM;
//        } else {
        } else {
//            *(char *)dstk = TAG_TOK; /* no need to mark end of string (we
//                                        suppose data is initied to zero */
			$this->stackMemory[$this->dstk] = TAG_TOK;
//            tok = strstr(sym_stk, last_id - 1) - sym_stk;
			$this->tok = strpos($this->sym_stk, $this->last_id - 1);
//            *(char *)dstk = 0;   /* mark real end of ident for dlsym() */
			$this->stackMemory[$this->dstk] = 0;
//            tok = tok * 8 + TOK_IDENT;
			$this->tok = $this->tok * 8 + self::TOK_IDENT;
//            if (tok > TOK_DEFINE) {
			if ($this->tok > self::TOK_DEFINE) {
//                tok = vars + tok;
				$this->tok = $this->vars + $this->tok;
                /*        printf("tok=%s %x\n", last_id, tok); */
				printf("tok=%s %x\n", $this->last_id, $this->tok);
                /* define handling */
//                if (*(int *)tok == SYM_DEFINE) {
				if ($this->tokmemory[$this->tok] == self::SYM_DEFINE) {
//                    dptr = *(int *)(tok + sizeof(int*));
					$this->dptr = $this->tokMemory[$this->tok + self::POINTERSIZE];
//                    dch = ch;
					$this->dch = $this->ch;
//                    inp();
					$this->inp();
//                    next();
                    $this->next();
//                }
				}
//            }
			}
//        }
		}
//    } else {
	} else {
//        inp();
        $this->inp();
//        if (tok == '\'') {
        if ($this->tok == '\'') {
//            tok = TOK_NUM;
            $this->tok = self::TOK_NUM;
//            getq();
            $this->getq();
//            tokc = ch;
            $this->tokc = $this->ch;
//            inp();
            $this->inp();
//            inp();
            $this->inp();
//        } else if (tok == '/' & ch == '*') {
        } else if ($this->tok == '/' & $this->ch == '*') {
//            inp();
            $this->inp();
//            while (ch) {
            while ($this->ch) {
//                while (ch != '*')
                while ($this->ch != '*')
//                    inp();
                    $this->inp();
//                inp();
                $this->inp();
//                if (ch == '/')
                if ($this->ch == '/')
//                    ch = 0;
                    $this->ch = 0;
//            }
            }
//            inp();
            $this->inp();
//            next();
            $this->next();
//        } else
        } else
//        {
        {
//            t = "++#m--%am*@R<^1c/@%[_[H3c%@%[_[H3c+@.B#d-@%:_^BKd<<Z/03e>>`/03e<=0f>=/f<@.f>@1f==&g!=\'g&&k||#l&@.BCh^@.BSi|@.B+j~@/%Yd!@&d*@b";
            $parserArray =  explode('',"++#m--%am*@R<^1c/@%[_[H3c%@%[_[H3c+@.B#d-@%:_^BKd<<Z/03e>>`/03e<=0f>=/f<@.f>@1f==&g!=\'g&&k||#l&@.BCh^@.BSi|@.B+j~@/%Yd!@&d*@b");
            $maxParserArray = count($parserArray);
//            while (l = *(char *)t++) {
            for($t = 0 ; $t < $maxParserArray ; $t ++ ) {
//                a = *(char *)t++;
                $a = $maxParserArray[$t++];
//                tokc = 0;
                $this->tokc = 0;
//                while ((tokl = *(char *)t++ - 'b') < 0)
                while (($this->tokl = chr($maxParserArray[$t++]) - chr('b')) < 0)
//                    tokc = tokc * 64 + tokl + 64;
                    $this->tokc = $this->tokc * 64 + $this->tokl + 64;
//                if (l == tok & (a == ch | a == '@')) {
                if ($l == $this->tok & ($a == $this->ch | $a == '@')) {
//#if 0
//                    printf("%c%c -> tokl=%d tokc=0x%x\n", 
//                           l, a, tokl, tokc);
//#endif
                    printf("%c%c -> tokl=%d tokc=0x%x\n", 
                           $l, $a, $this->tokl, $this->tokc);
//                    if (a == ch) {
                    if ($a == $this->ch) {
//                        inp();
                        $this->inp();
//                        tok = TOK_DUMMY; /* dummy token for double tokens */
                        $this->tok = self::TOK_DUMMY; /* dummy token for double tokens */
//                    }
                    }
//                    break;
                    break;
//                }
                }
//            }
            }
//        }
        }
//    }
    }
/*
#if 0
    {
        int p;

        printf("tok=0x%x ", tok);
        if (tok >= TOK_IDENT) {
            printf("'");
            if (tok > TOK_DEFINE) 
                p = sym_stk + 1 + (tok - vars - TOK_IDENT) / 8;
            else
                p = sym_stk + 1 + (tok - TOK_IDENT) / 8;
            while (*(char *)p != TAG_TOK && *(char *)p)
                printf("%c", *(char *)p++);
            printf("'\n");
        } else if (tok == TOK_NUM) {
            printf("%d\n", tokc);
        } else {
            printf("'%c'\n", tok);
        }
    }
#endif
*/
    {
        printf("tok=0x%x ", $this->tok);
        if ($this->tok >= self::TOK_IDENT) {
            printf("'");
            if ($this->tok > self::TOK_DEFINE) 
                $p = 1 + ($this->tok - $this->vars - self::TOK_IDENT) / 8;
            else
                $p = 1 + ($this->tok - self::TOK_IDENT) / 8;
            while ($this->sym_stk[$p] != self::TAG_TOK && $this->sym_stk[$p] ) {
                printf("%c", $this->sym_stk[$p++] );
            }
            printf("'\n");
        } else if ($this->tok == self::TOK_NUM) {
            printf("%d\n", $this->tokc);
        } else {
            printf("'%c'\n", $this->tokc);
        }
    }
}

//#ifdef TINY
//#define skip(c) next()
//#else

//void error(char *fmt,...)
//{
//    va_list ap;
//
//    va_start(ap, fmt);
//    fprintf(stderr, "%d: ", ftell((FILE *)file));
//    vfprintf(stderr, fmt, ap);
//    fprintf(stderr, "\n");
//    exit(1);
//    va_end(ap);
//}

//void skip(c)
function skip($c)
//{
{
//    if (tok != c) {
    if ($this->tok != $c) {
//        error("'%c' expected", c);
        error("'%c' expected", $c);
//    }
    next();
//    next();
//}
}

//#endif

/* from 0 to 4 bytes */
//o(n)
function o($n)
//{
{
    /* cannot use unsigned, so we must do a hack */
//    while (n && n != -1) {
    while ($n && $n != -1) {
//        *(char *)ind++ = n;
        $this->indAlloc[$ind++] = $n;
//        n = n >> 8;
        $n = $n >> 8;
//    }
    }
//}
}

//#ifdef ELFOUT

/* put a 32 bit little endian word 'n' at unaligned address 't' */
//put32(t, n)
function put32($t, $n)
//{
{
//    *(char *)t++ = n;
    $this->indAlloc[$t++] = $n;
//    *(char *)t++ = n >> 8;
    $this->indAlloc[$t++] = n >> 8;
//    *(char *)t++ = n >> 16;
    $this->indAlloc[$t++] = $n >> 16;
//    *(char *)t++ = n >> 24;
    $this->indAlloc[$t++] = $n >> 24;
//}
}

/* get a 32 bit little endian word at unaligned address 't' */
//get32(t)
function get32($t)
//{
{
//    int n;
//    return  (*(char *)t & 0xff) |
//        (*(char *)(t + 1) & 0xff) << 8 |
//        (*(char *)(t + 2) & 0xff) << 16 |
//        (*(char *)(t + 3) & 0xff) << 24;
    return  ($this->indAlloc[$t] & 0xff) |
        ($this->indAlloc[$t+1] & 0xff) << 8 |
        ($this->indAlloc[$t+2] & 0xff) << 16 |
        ($this->indAlloc[$t+3] & 0xff) << 24;
//}
}

//#else
//
//#define put32(t, n) *(int *)t = n
//#define get32(t) *(int *)t
//
//#endif

/* output a symbol and patch all references to it */
//gsym1(t, b)
function gsym1($t, $b)
//{
{
//    int n;
//    while (t) {
    while ($this->indAlloc[$t]) {
//        n = get32(t); /* next value */
        $n = $this->get32($t); /* next value */
        /* patch absolute reference (always mov/lea before) */
//        if (*(char *)(t - 1) == 0x05) {
        if ($this->indAlloc[$t - 1] == 0x05) {
            /* XXX: incorrect if data < 0 */
//            if (b >= data && b < glo)
            if ($b >= $this->data && $this->b < $this->glo)
//                put32(t, b + data_offset);
                $this->put32($t, $this->b + $this->data_offset);
//            else
            else
//                put32(t, b - prog + text + data_offset);
                $this->put32($t, $b - $this->prog + $this->text + $this->data_offset);
//        } else {
        } else {
//            put32(t, b - t - sizeof(int*));
            $this->put32($t, $b - $t - self::POINTERSIZE);
//        }
        }
//        t = n;
        $t = $n;
//    }
    }
//}
}

//gsym(t)
function gsym($t)
//{
{
//    gsym1(t, ind);
    $this->gsym1($t, $this->ind);
//}
}

/* psym is used to put an instruction with a data field which is a
   reference to a symbol. It is in fact the same as oad ! */
#define psym oad

/* instruction + address */
//oad(n, t)
function oad($n, $t)
//{
{
//    o(n);
    $this->o($n);
//    put32(ind, t);
    $this->put32($this->ind, $t);
//    t = ind;
    $t = $this->ind;
//    ind = ind + sizeof(int*);
    $this->ind = $this->ind + self::POINTERSIZE;
//    return t;
    return $t;
//}
}

/* load immediate value */
//li(t)
function li($t)
//{
{
//    oad(0xb8, t); /* mov $xx, %eax */
    $this->oad(0xb8, $t); /* mov $xx, %eax */
//}
}

//gjmp(t)
function gjmp($t)
//{
{
//    return psym(0xe9, t);
    return $this->psym(0xe9, $t);
//}
}

/* l = 0: je, l == 1: jne */
//gtst(l, t)
function gtst($l, $t)
//{
{
//    o(0x0fc085); /* test %eax, %eax, je/jne xxx */
    $this->o(0x0fc085); /* test %eax, %eax, je/jne xxx */
//    return psym(0x84 + l, t);
    return $this->psym(0x84 + $l, $t);
//}
}

//gcmp(t)
function gcmp($t)
//{
{
//    o(0xc139); /* cmp %eax,%ecx */
    $this->o(0xc139); /* cmp %eax,%ecx */
//    li(0);
    $this->li(0);
//    o(0x0f); /* setxx %al */
    $this->o(0x0f); /* setxx %al */
//    o(t + 0x90);
    $this->o($t + 0x90);
//    o(0xc0);
    $this->o(0xc0);
//}
}

//gmov(l, t)
function gmov($l, $t)
//{
{
//    int n;
//    o(l + 0x83);
    $this->o($l + 0x83);
//    n = *(int *)t;
    $n = $this->tokMemory[$t];
//    if (n && n < LOCAL)
    if ($n && $n < LOCAL)
//        oad(0x85, n);
        $this->oad(0x85, $this->n);
//    else {
    else {
//        t = t + sizeof(int*);
        $t = $t + SELF::POINTERSIZE;
//        *(int *)t = psym(0x05, *(int *)t);
        $this->tokMemory[$t] = $this->psym(0x05, $this->tokMemory[$t]);
//    }
    }
//}
}

/* l is one if '=' parsing wanted (quick hack) */
//unary(l)
function unary($l)
//{
{
//    int n, t, a, c;
//
//    n = 1; /* type of expression 0 = forward, 1 = value, other =
//              lvalue */
    $n = 1;
//    if (tok == '\"') {
    if ($this->tok == '\"') {
//        li(glo + data_offset);
        $this->li($this->glo + $this->data_offset);
//        while (ch != '\"') {
        while ($this->ch != '\"') {
//            getq();
            $this->getq();
//            *(char *)glo++ = ch;
            $this->gloMemory[$this->glo++] = $this->ch;
//            inp();
            $this->inp();
//        }
        }
//        *(char *)glo = 0;
        $this->gloMemory[$this->glo] = 0;
//        glo = glo + sizeof(int*) & -4; /* align heap */
        $this->glo = $this->glo + self::POINTERSIZE & -4; /* align heap */
//        inp();
        $this->inp();
//        next();
        $this->next();
//    } else {
    } else {
//        c = tokl;
        $c = $this->tokl;
//        a = tokc;
        $a = $this->tokc;
//        t = tok;
        $t = $this->tok;
//        next();
        $this->next();
//        if (t == TOK_NUM) {
        if ($t == self::TOK_NUM) {
            $this->li($a);
//        } else if (c == 2) {
        } else if ($c == 2) {
            /* -, +, !, ~ */
//            unary(0);
            $this->unary(0);
//            oad(0xb9, 0); /* movl $0, %ecx */
            $this->oad(0xb9, 0); /* movl $0, %ecx */
//            if (t == '!')
            if ($t == '!')
//                gcmp(a);
                $this->gcmp($a);
//            else
            else
//                o(a);
                $this->o($a);
//        } else if (t == '(') {
        } else if ($t == '(') {
//            expr();
            $this->expr();
//            skip(')');
            $this->skip(')');
//        } else if (t == '*') {
        } else if ($t == '*') {
            /* parse cast */
//            skip('(');
            $this->skip('(');
//            t = tok; /* get type */
            $t = $this->tok; /* get type */
//          next(); /* skip int/char/void */
            $this->next(); /* skip int/char/void */
//            next(); /* skip '*' or '(' */
            $this->next(); /* skip '*' or '(' */
//            if (tok == '*') {
            if ($this->tok == '*') {
                /* function type */
//                skip('*');
                $this->skip('*');
//                skip(')');
                $this->skip(')');
//                skip('(');
                $this->skip('(');
//                skip(')');
                $this->skip(')');
//                t = 0;
                $t = 0;
//            }
            }
//            skip(')');
            $this->skip(')');
//            unary(0);
            $this->unary(0);
//            if (tok == '=') {
            if ($this->tok == '=') {
//                next();
                $this->next();
//                o(0x50); /* push %eax */
                $this->o(0x50); /* push %eax */
//                expr();
                $this->expr();
//                o(0x59); /* pop %ecx */
                $this->o(0x59); /* pop %ecx */
//                o(0x0188 + (t == TOK_INT)); /* movl %eax/%al, (%ecx) */
                $this->o(0x0188 + ($t == self::TOK_INT)); /* movl %eax/%al, (%ecx) */
//            } else if (t) {
            } else if ($t) {
//                if (t == TOK_INT)
                if ($t == self::TOK_INT)
//                    o(0x8b); /* mov (%eax), %eax */
                    $this->o(0x8b); /* mov (%eax), %eax */
//                else 
                else 
//                    o(0xbe0f); /* movsbl (%eax), %eax */
                    $this->o(0xbe0f); /* movsbl (%eax), %eax */
//                ind++; /* add zero in code */
                $this->ind++; /* add zero in code */
//            }
            }
//        } else if (t == '&') {
        } else if ($t == '&') {
//            gmov(10, tok); /* leal EA, %eax */
            $this->gmov(10, $this->tok); /* leal EA, %eax */
//            next();
            $this->next();
//        } else {
        } else {
//            n = 0;
            $n = 0;
//            if (tok == '=' & l) {
            if ($this->tok == '=' & $l) {
                /* assignment */
//                next();
                $this->next();
//                expr();
                $this->expr();
//                gmov(6, t); /* mov %eax, EA */
                $this->gmov(6, $t); /* mov %eax, EA */
//            } else if (tok != '(') {
            } else if ($this->tok != '(') {
                /* variable */
//                gmov(8, t); /* mov EA, %eax */
                $this->gmov(8, $t); /* mov EA, %eax */
//                if (tokl == 11) {
                if ($this->tokl == 11) {
//                    gmov(0, t);
                    $this->gmov(0, $t);
//                    o(tokc);
                    $this->o($this->tokc);
//                    next();
                    $this->next();
//                }
                }
//            }
            }
//        }
        }
//    }
    }

    /* function call */
//    if (tok == '(') {
    if ($this->tok == '(') {
//        if (n)
        if ($n)
//            o(0x50); /* push %eax */
            $this->o(0x50); /* push %eax */

        /* push args and invert order */
//        a = oad(0xec81, 0); /* sub $xxx, %esp */
        $a = $this->oad(0xec81, 0); /* sub $xxx, %esp */
//        next();
        $this->next();
//        l = 0;
        $l = 0;
//        while(tok != ')') {
        while($this->tok != ')') {
//            expr();
            $this->expr();
//            oad(0x248489, l); /* movl %eax, xxx(%esp) */
            $this->oad(0x248489, $l); /* movl %eax, xxx(%esp) */
//            if (tok == ',')
            if ($this->tok == ',')
//                next();
                $this->next();
//            l = l + sizeof(int*);
            $l = $l + self::POINTERSIZE;
//        }
        }
//        put32(a, l);
        $this->put32($a, $l);
//        next();
        $this->next();
//        if (n) {
        if ($n) {
//            oad(0x2494ff, l); /* call *xxx(%esp) */
            $this->oad(0x2494ff, $l); /* call *xxx(%esp) */
//            l = l + sizeof(int*);
            $l = $l + self::POINTERSIZE;
//        } else {
        } else {
            /* forward reference */
//            t = t + sizeof(int*);
            $t = $t + slef::POINTERSIZE;
//            *(int *)t = psym(0xe8, *(int *)t);
            $this->tokMemory[$t] = $this->psym(0xe8, $this->tokMemory[$t]);
//        }
        }
//        if (l)
        if ($l)
//            oad(0xc481, l); /* add $xxx, %esp */
            $this->oad(0xc481, $this->l); /* add $xxx, %esp */
//    }
    }
//}
}

//sum(l)
function sum($l)
//{
{
//    int t, n, a;

//    if (l-- == 1)
    if ($l-- == 1)
//        unary(1);
        $this->unary(1);
//    else {
    else {
//        sum(l);
        $this->sum($this->l);
//        a = 0;
        $a = 0;
//        while (l == tokl) {
        while ($l == $this->tokl) {
//            n = tok;
            $n = $this->tok;
//            t = tokc;
            $t = $this->tokc;
//            next();
            $this->next();

//            if (l > 8) {
            if ($l > 8) {
//                a = gtst(t, a); /* && and || output code generation */
                $a = $this->gtst($t, $a); /* && and || output code generation */
//                sum(l);
                $this->sum($l);
//            } else {
            } else {
//                o(0x50); /* push %eax */
                $this->o(0x50); /* push %eax */
//                sum(l);
                $this->sum($l);
//                o(0x59); /* pop %ecx */
                $this->o(0x59); /* pop %ecx */
                
//                if (l == 4 | l == 5) {
                if ($l == 4 | $l == 5) {
//                    gcmp(t);
                    $this->gcmp($t);
//                } else {
                } else {
//                    o(t);
                    $this->o($t);
//                    if (n == '%')
                    if ($n == '%')
//                        o(0x92); /* xchg %edx, %eax */
                        $this->o(0x92); /* xchg %edx, %eax */
//                }
                }
//            }
            }
//        }
        }
        /* && and || output code generation */
//        if (a && l > 8) {
        if ($a && $l > 8) {
//            a = gtst(t, a);
            $a = $this->gtst($t, $a);
//            li(t ^ 1);
            $this->li($t ^ 1);
//            gjmp(5); /* jmp $ + 5 */
            $this->gjmp(5); /* jmp $ + 5 */
//            gsym(a);
            $this->gsym($a);
//            li(t);
            $this->li($t);
//        }
        }
//    }
    }
//}
}

//expr()
function expr()
//{
{
//    sum(11);
    $this->sum(11);
//}
}


//test_expr()
function test_expr()
//{
{
//    expr();
    $this->expr();
//    return gtst(0, 0);
    return $this->gtst(0, 0);
//}
}

//block(l)
function block($l)
//{
{
//    int a, n, t;

//    if (tok == TOK_IF) {
    if ($this->tok == self::TOK_IF) {
//        next();
        $this->next();
//        skip('(');
        $this->skip('(');
//        a = test_expr();
        $a = $this->test_expr();
//        skip(')');
        $this->skip(')');
//        block(l);
        $this->block($l);
//        if (tok == TOK_ELSE) {
        if ($this->tok == self::TOK_ELSE) {
//            next();
            $this->next();
//            n = gjmp(0); /* jmp */
            $n = $this->gjmp(0); /* jmp */
//            gsym(a);
            $this->gsym($a);
//            block(l);
            $this->block($l);
//            gsym(n); /* patch else jmp */
            $this->gsym($n); /* patch else jmp */
//        } else {
        } else {
//            gsym(a); /* patch if test */
            $this->gsym($a); /* patch if test */
//        }
        }
//    } else if (tok == TOK_WHILE | tok == TOK_FOR) {
    } else if ($this->tok == self::TOK_WHILE | $this->tok == self::TOK_FOR) {
//        t = tok;
        $t = $this->tok;
//        next();
        $this->next();
//        skip('(');
        $this->skip('(');
//        if (t == TOK_WHILE) {
        if ($t == self::TOK_WHILE) {
//            n = ind;
            $n = $this->ind;
//            a = test_expr();
            $a = $this->test_expr();
//        } else {
        } else {
//            if (tok != ';')
            if ($this->tok != ';')
//                expr();
                $this->expr();
//            skip(';');
            $this->skip(';');
//            n = ind;
            $n = $this->ind;
//            a = 0;
            $a = 0;
//            if (tok != ';')
            if ($this->tok != ';')
//                a = test_expr();
                $a = $this->test_expr();
//            skip(';');
            $this->skip(';');
//            if (tok != ')') {
            if ($this->tok != ')') {
//                t = gjmp(0);
                $t = $this->gjmp(0);
//                expr();
                $this->expr();
//                gjmp(n - ind - 5);
                $this->gjmp($n - $this->ind - 5);
//                gsym(t);
                $this->gsym($t);
//                n = t + sizeof(int*);
                $n = $t + slef::POINTERSIZE;
//            }
            }
//        }
        }
//        skip(')');
        $this->skip(')');
//        block(&a);
        $this->block(&a);
//        gjmp(n - ind - 5); /* jmp */
        $this->gjmp($n - $this->ind - 5); /* jmp */
//        gsym(a);
        $this->gsym($a);
//    } else if (tok == '{') {
    } else if ($this->tok == '{') {
//        next();
        $this->next();
        /* declarations */
//        decl(1);
        $this->decl(1);
//        while(tok != '}')
        while($this->tok != '}')
//            block(l);
            $this->block($l);
//        next();
        $this->next();
//    } else {
    } else {
//        if (tok == TOK_RETURN) {
        if ($this->tok == TOK_RETURN) {
//            next();
            $this->next();
//            if (tok != ';')
            if ($this->tok != ';')
//                expr();
                $this->expr();
//            rsym = gjmp(rsym); /* jmp */
            $this->rsym = $this->gjmp($this->rsym); /* jmp */
//        } else if (tok == TOK_BREAK) {
        } else if ($this->tok == self::TOK_BREAK) {
//            next();
            $this->next();
//            *(int *)l = gjmp(*(int *)l);
            $this->tokMemory[$l] = $this->gjmp($this->tokMemory[$l]);
//        } else if (tok != ';')
        } else if ($this->tok != ';')
//            expr();
            $this->expr();
//        skip(';');
        $this->skip(';');
//    }
    }
//}
}

/* 'l' is true if local declarations */
//decl(l)
function decl($l)
//{
//    int a;

//    while (tok == TOK_INT | tok != -1 & !l) {
    while ($this->tok == self::TOK_INT | $this->tok != -1 & !$l) {
//        if (tok == TOK_INT) {
        if ($this->tok == self::TOK_INT) {
//            next();
            $this->next();
//            while (tok != ';') {
            while ($this->tok != ';') {
//                if (l) {
                if ($l) {
//                    loc = loc + sizeof(int*);
                    $this->loc = $this->loc + self::POINTERSIZE;
//                    *(int *)tok = -loc;
                    $this->tokMemory[$tok] = -$loc;
//                } else {
                } else {
//                    *(int *)tok = glo;
                    $this->tokMemory[$tok] = $glo;
//                    glo = glo + sizeof(int*);
                    $this->glo = $this->glo + self::POINTERSIZE;
//                }
                }
//                next();
                $this->next();
//                if (tok == ',') 
                if ($this->tok == ',') 
//                    next();
                    $this->next();
//            }
            }
//            skip(';');
            $this->skip(';');
//        } else {
        } else {
            /* put function address */
//            *(int *)tok = ind;
            $this->tokMemory[$tok] = $ind;
//            next();
            $this->next();
//            skip('(');
            $this->skip('(');
//            a = 8;
            $a = 8;
//            while (tok != ')') {
            while ($this->tok != ')') {
                /* read param name and compute offset */
//                *(int *)tok = a;
                $this->tokMemory[$tok] = $a;
//                a = a + sizeof(int*);
                $a = $a + self::POINTERSIZE;
//                next();
                $this->next();
//                if (tok == ',')
                if ($this->tok == ',')
//                    next();
                    $this->next();
//            }
            }
//            next(); /* skip ')' */
            $this->next(); /* skip ')' */
//            rsym = loc = 0;
            $this->rsym = $this->loc = 0;
//            o(0xe58955); /* push   %ebp, mov %esp, %ebp */
            $this->o(0xe58955); /* push   %ebp, mov %esp, %ebp */
//            a = oad(0xec81, 0); /* sub $xxx, %esp */
            $a = $this->oad(0xec81, 0); /* sub $xxx, %esp */
//            block(0);
            $this->block(0);
//            gsym(rsym);
            $this->gsym($this->rsym);
//            o(0xc3c9); /* leave, ret */
            $this->o(0xc3c9); /* leave, ret */
//            put32(a, loc); /* save local variables */
            $this->put32($a, $this->loc); /* save local variables */
//        }
        }
//    }
    }
//}
}

//#ifdef ELFOUT
//gle32(n)
function gle32($n)
//{
{
//    put32(glo, n);
    $this->put32($this->glo, $n);
//    glo = glo + sizeof(int*);
    $this->glo = $this->glo + self::POINTERSIZE;
//}
}

/* used to generate a program header at offset 't' of size 's' */
//gphdr1(n, t)
function gphdr1($n, $t)
//{
{
//    gle32(n);
    $this->gle32($n);
//    n = n + ELF_BASE;
    $n = $n + self::ELF_BASE;
//    gle32(n);
    $this->gle32($n);
//    gle32(n);
    $this->gle32($n);
//    gle32(t);
    $this->gle32($t);
//    gle32(t);
    $this->gle32($t);
//}
}

//elf_reloc(l)
function elf_reloc($l)
//{
{
//    int t, a, n, p, b, c;

//    p = 0;
    $p = 0;
//    t = sym_stk;
    $t = 0;
//    while (1) {
    while (1) {
        /* extract symbol name */
//        t++;
        $t++;
//        a = t;
        $a = $t;
//        while (*(char *)t != TAG_TOK && t < dstk)
        while ($this->sym_stk[$t] != self::TAG_TOK && $t < $this->dstk)
//            t++;
            $t++;
//        if (t == dstk)
        if ($t == $this->dstk)
//            break;
            break;
        /* now see if it is forward defined */
//        tok = vars + (a - sym_stk) * 8 + TOK_IDENT - 8;
        $this->tok = $this->vars + ($this->a) * 8 + self::TOK_IDENT - 8;
//        b = *(int *)tok;
        $b = $this->tok;
//        n = *(int *)(tok + sizeof(int*));
        $n = $this->tok + self::POINTERSIZE;
//        if (n && b != 1) {
        if ($n && $b != 1) {
/*
#if 0
            {
                char buf[100];
                memcpy(buf, a, t - a);
                buf[t - a] = '\0';
                printf("extern ref='%s' val=%x\n", buf, b);
            }
#endif
*/
            substr($a,)
            if (!b) {
                if (!l) {
                    /* symbol string */
                    memcpy(glo, a, t - a);
                    glo = glo + t - a + 1; /* add a zero */
                } else if (l == 1) {
                    /* symbol table */
                    gle32(p + DYNSTR_BASE);
                    gle32(0);
                    gle32(0);
                    gle32(0x10); /* STB_GLOBAL, STT_NOTYPE */
                    p = p + t - a + 1; /* add a zero */
                } else {
                    p++;
                    /* generate relocation patches */
                    while (n) {
                        a = get32(n);
                        /* c = 0: R_386_32, c = 1: R_386_PC32 */
                        c = *(char *)(n - 1) != 0x05;
                        put32(n, -c * sizeof(int*));
                        gle32(n - prog + text + data_offset);
                        gle32(p * 256 + c + 1);
                        n = a;
                    }
                }
            } else if (!l) {
                /* generate standard relocation */
                gsym1(n, b);
            }
        }
    }
}

elf_out(c)
{
    int glo_saved, dynstr, dynstr_size, dynsym, hash, rel, n, t, text_size;

    /*****************************/
    /* add text segment (but copy it later to handle relocations) */
    text = glo;
    text_size = ind - prog;

    /* add the startup code */
    ind = prog;
    o(0x505458); /* pop %eax, push %esp, push %eax */
    t = *(int *)(vars + TOK_MAIN);
    oad(0xe8, t - ind - 5);
    o(0xc389);  /* movl %eax, %ebx */
    li(1);      /* mov $1, %eax */
    o(0x80cd);  /* int $0x80 */
    glo = glo + text_size;

    /*****************************/
    /* add symbol strings */
    dynstr = glo;
    /* libc name for dynamic table */
    glo++;
    glo = strcpy(glo, "libc.so.6") + 10;
    glo = strcpy(glo, "libdl.so.2") + 11;
    
    /* export all forward referenced functions */
    elf_reloc(0);
    dynstr_size = glo - dynstr;

    /*****************************/
    /* add symbol table */
    glo = (glo + 3) & -sizeof(int*);
    dynsym = glo;
    gle32(0);
    gle32(0);
    gle32(0);
    gle32(0);
    elf_reloc(1);

    /*****************************/
    /* add symbol hash table */
    hash = glo;
    n = (glo - dynsym) / 16;
    gle32(1); /* one bucket (simpler!) */
    gle32(n);
    gle32(1);
    gle32(0); /* dummy first symbol */
    t = 2;
    while (t < n)
        gle32(t++);
    gle32(0);
    
    /*****************************/
    /* relocation table */
    rel = glo;
    elf_reloc(2);

    /* copy code AFTER relocation is done */
    memcpy(text, prog, text_size);

    glo_saved = glo;
    glo = data;

    /* elf header */
    gle32(0x464c457f);
    gle32(0x00010101);
    gle32(0);
    gle32(0);
    gle32(0x00030002);
    gle32(1);
    gle32(text + data_offset); /* address of _start */
    gle32(PHDR_OFFSET); /* offset of phdr */
    gle32(0);
    gle32(0);
    gle32(0x00200034);
    gle32(3); /* phdr entry count */

    /* program headers */
    gle32(3); /* PT_INTERP */
    gphdr1(INTERP_OFFSET, INTERP_SIZE);
    gle32(4); /* PF_R */
    gle32(1); /* align */
    
    gle32(1); /* PT_LOAD */
    gphdr1(0, glo_saved - data);
    gle32(7); /* PF_R | PF_X | PF_W */
    gle32(0x1000); /* align */
    
    gle32(2); /* PT_DYNAMIC */
    gphdr1(DYNAMIC_OFFSET, DYNAMIC_SIZE);
    gle32(6); /* PF_R | PF_W */
    gle32(0x4); /* align */

    /* now the interpreter name */
    glo = strcpy(glo, "/lib/ld-linux.so.2") + 0x14;

    /* now the dynamic section */
    gle32(1); /* DT_NEEDED */
    gle32(1); /* libc name */
    gle32(1); /* DT_NEEDED */
    gle32(11); /* libdl name */
    gle32(4); /* DT_HASH */
    gle32(hash + data_offset);
    gle32(6); /* DT_SYMTAB */
    gle32(dynsym + data_offset);
    gle32(5); /* DT_STRTAB */
    gle32(dynstr + data_offset);
    gle32(10); /* DT_STRSZ */
    gle32(dynstr_size);
    gle32(11); /* DT_SYMENT */
    gle32(16);
    gle32(17); /* DT_REL */
    gle32(rel + data_offset);
    gle32(18); /* DT_RELSZ */
    gle32(glo_saved - rel);
    gle32(19); /* DT_RELENT */
    gle32(8);
    gle32(0);  /* DT_NULL */
    gle32(0);

    t = fopen(c, "w");
    fwrite(data, 1, glo_saved - data, t);
    fclose(t);
}
//#endif

main(int n,char** t)
{
    if (n < 3) {
        printf("usage: otccelf file.c outfile\n");
        return 0;
    }
    dstk = strcpy(sym_stk = calloc(1, ALLOC_SIZE), 
                  " int if else while break return for define main ") + TOK_STR_SIZE;
    glo = data = calloc(1, ALLOC_SIZE);
    ind = prog = calloc(1, ALLOC_SIZE);
    vars = calloc(1, ALLOC_SIZE);

    file = fopen(t[1], "r");

    data_offset = ELF_BASE - data; 
    glo = glo + ELFSTART_SIZE;
    ind = ind + STARTUP_SIZE;

    inp();
    next();
    decl(0);
    elf_out(t[2]);
    return 0;
}
