use strict;

use Fcntl ':flock';

sub msg { print strftime("%d.%m.%y %H:%M:%S", localtime)."  @_\n"; }

sub touch
{
	my $fname = shift;
	if (open my $file, ">>$fname") {
		close $file;
	}
}

sub norm_loc
{
	my $out = shift;
	return $out>180?$out-360:$out;
}

sub to_utf
{
	my $out = shift;
	from_to($out, "windows-1251", "utf-8");
	return $out;
}

sub from_utf
{
	my $out = shift;
	from_to($out, "utf-8", "windows-1251");
	return $out;
}

sub read_file
{
	my $fname = shift;
	local $/;

	return "" unless open my ($file), $fname;

	my $out = <$file>;
	close $file;

	return $out;
}

sub xml_parse
{
	my $data = read_file(shift);

	my @tags;
	my $tag_c = -1;

	push @tags, $1 while $data =~ /(<.*?>)/gs;

	return xml_parse_2(\@tags, \$tag_c);
}

sub xml_parse_s
{
	my $data = shift;

	my @tags;
	my $tag_c = -1;

	push @tags, $1 while $data =~ /(<.*?>)/gs;

	return xml_parse_2(\@tags, \$tag_c);
}

sub xml_parse_2
{
	my ($tags, $tag_c) = @_;
	my @ready;

	while ($$tag_c < $#$tags) {
		my $tag = $$tags[++$$tag_c];

		if ($tag =~ /^<\/(.+)>$/s) {
			last;
		} elsif ($tag =~ /^<(.+?)(\s+(.+?))?\s*\/>$/s) {
			my ($name, $data) = ($1, $2);
			push @ready, {name => $name, data => xml_parse_3($data)};
		} elsif ($tag =~ /^<(.+?)(\s+(.+?))?\s*>$/s) {
			my ($name, $data) = ($1, $2);
			push @ready, {name => $name, data => xml_parse_3($data), child => xml_parse_2($tags, $tag_c)};
		}
	}

	return \@ready;
}

sub xml_parse_3
{
	my $data = shift;
	my %ready;

	if ($data) {
		while ($data =~ s/^\s*(.+?)\s*=\s*//sg) {
			my $name = $1;
			my $value;

			if($data =~ /^\"/s) {
				$data =~ s/^\"(.*?)\"//s;
				$value = $1;
			} else {
				$data =~ s/^(\S*)//s;
				$value = $1;
			}

			$ready{$name} = $value;
		}
	}

	return \%ready;
}

sub xml_to_str
{
	my $xml = shift;

	my @data;
	push @data, qq~$_="$xml->{data}{$_}"~ foreach sort keys %{$xml->{data}};

	return "<$xml->{name} ".join(" ", @data)."/>" unless $xml->{child};

	my @child;
	push @child, xml_to_str($_) foreach @{$xml->{child}};

	return "<$xml->{name} ".join(" ", @data).">".join("", @child)."</$xml->{name}>";
}

sub add_money
{
	my ($id, $login, $count, $text) = @_;
	our $logs;

	return 0 unless open my ($file), "+<$logs/money-$id.dat";

	flock $file, LOCK_EX;

	my %account = split /\t/, <$file>;

	$account{$login} += $count;

	if (open my ($log), ">>$logs/money-$id-".strftime("%Y%m%d", localtime).".log") {
		print $log strftime("%d.%m.%y %H:%M:%S", localtime)."\t$login\t+$count\t$account{$login}\t$text\n";
		close $log;
	}

	seek $file, 0, SEEK_SET;
	truncate $file, 0;

	print $file join "\t", %account;

	flock $file, LOCK_UN;
	close $file;

	return 1;
}

sub del_money
{
	my ($id, $login, $count, $text) = @_;
	our $logs;

	return 0 unless open my ($file), "+<$logs/money-$id.dat";

	flock $file, LOCK_EX;

	my %account = split /\t/, <$file>;

	$account{$login} -= $count;

	if (open my ($log), ">>$logs/money-$id-".strftime("%Y%m%d", localtime).".log") {
		print $log strftime("%d.%m.%y %H:%M:%S", localtime)."\t$login\t-$count\t$account{$login}\t$text\n";
		close $log;
	}

	delete $account{$login} if $account{$login} <= 0;

	seek $file, 0, SEEK_SET;
	truncate $file, 0;

	print $file join "\t", %account;

	flock $file, LOCK_UN;
	close $file;

	return 1;
}

sub log_money
{
	my ($id, $login, $text) = @_;
	our $logs;

	return -1 unless open my ($file), "<$logs/money-$id.dat";

	flock $file, LOCK_EX;

	my %account = split /\t/, <$file>;

	if (open my ($log), ">>$logs/money-$id-".strftime("%Y%m%d", localtime).".log") {
		print $log strftime("%d.%m.%y %H:%M:%S", localtime)."\t$login\t\t".($account{$login}||0)."\t$text\n";
		close $log;
	}

	flock $file, LOCK_UN;
	close $file;
}

sub get_money
{
	my ($id, $login) = @_;
	our $logs;

	return -1 unless open my ($file), "<$logs/money-$id.dat";

	flock $file, LOCK_EX;

	my %account = split /\t/, <$file>;

	flock $file, LOCK_UN;
	close $file;

	return $account{$login}||0;
}

sub html_escape
{
	my $str = shift;

	$str =~ s{&}{&amp;}gs;
	$str =~ s{"}{&quot;}gs;
	$str =~ s{'}{&apos;}gs;
	$str =~ s{<}{&lt;}gs;
	$str =~ s{>}{&gt;}gs;

	return $str;
}

sub html_unescape
{
	my $str = shift;

	$str =~ s{&quot;}{"}gs;
	$str =~ s{&apos;}{'}gs;
	$str =~ s{&lt;}{<}gs;
	$str =~ s{&gt;}{>}gs;
	$str =~ s{&#037;}{:}gs;
	$str =~ s{&amp;}{&}gs;

	return $str;
}

1;
