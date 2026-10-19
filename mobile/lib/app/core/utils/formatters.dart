import 'package:intl/intl.dart';

abstract final class Formatters {
  static final _money = NumberFormat.decimalPattern('fr_FR');

  static String money(int amount) => amount == 0 ? 'Gratuit' : '${_money.format(amount)} F';

  static String time(DateTime date) => DateFormat.Hm('fr_FR').format(date);

  static String dayTime(DateTime date) => DateFormat("EEE d MMM 'à' HH:mm", 'fr_FR').format(date);

  static String day(DateTime date) => DateFormat('EEEE d MMMM', 'fr_FR').format(date);
}
