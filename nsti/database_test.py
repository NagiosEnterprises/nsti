import unittest
import database

class DatabaseTest(unittest.TestCase):

	def setUp(self):
		pass

	def test_get_queryable_keys(self):
		sample = {}
		result = database.get_queryable_keys(database.Snmptt, sample)
		self.assertEquals(result, sample)

		sample['foo'] = 'bongo'
		result = database.get_queryable_keys(database.Snmptt, sample)
		self.assertEquals(result, {})

		sample['timewritten'] = 'bingo'
		result = database.get_queryable_keys(database.Snmptt, sample)
		self.assertEquals(result, {'timewritten': 'bingo'})

		sample['hostname__contains'] = 'localhost'
		result = database.get_queryable_keys(database.Snmptt, sample)
		self.assertEquals(result, {'hostname__contains': 'localhost', 'timewritten': 'bingo'})

		sample['hotname__in'] = ['garfield', 'odie']
		result = database.get_queryable_keys(database.Snmptt, sample)
		self.assertEquals(result, {'hostname__contains': 'localhost', 'timewritten': 'bingo'})

	def test_get_combine(self):
		sample = {}
		result = database.get_combiner(sample)
		self.assertTrue(result)

		sample['combiner'] = 'or'
		result = database.get_combiner(sample)
		self.assertFalse(result)

	def test_add_active_filters_to_queryable(self):
		sample = {}
		active = ['predator', 'prey']
		result = database.get_active_filters_as_queryable(sample, active)
		self.assertEquals(result, sample)

		sample['predator'] = {'actions': [{'comparison': '__contains', 'value': 'localhost', 'column_name': 'hostname'}]}
		result = database.get_active_filters_as_queryable(sample, active)
		self.assertEquals(result, {'hostname__contains': 'localhost'})

		sample['aliens'] = {'actions': [{'comparison': '__contains', 'value': 'localhost', 'column_name': 'timewritten'}]}
		result = database.get_active_filters_as_queryable(sample, active)
		self.assertEquals(result, {'hostname__contains': 'localhost'})