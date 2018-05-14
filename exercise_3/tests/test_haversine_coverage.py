import sys
import os
import unittest

sys.path.append(os.path.abspath('./haversine'))

from haversine_coverage import CoverageMockData, Coverage


class TestHaversineCoverage(unittest.TestCase):

    def setUp(self):
        """
        Random data is reused only to save time.
        Real life tests should use preformed mock data in order to perform
        reliable tests.
        """
        self.locations = CoverageMockData.get_locations()
        self.shoppers = CoverageMockData.get_enabled_shoppers()
        self.coverage = Coverage.calculate_coverage(self.locations,
                                                    self.shoppers)

    def test_number_of_shoppers(self):
        unique_shoppers = len(list({k['id']:k for k in self.shoppers}.values()))
        self.assertEqual(unique_shoppers, len(self.coverage))

    def test_coverage_is_positive(self):
        coverage_values = [k['coverage'] for k in self.coverage
                           if k['coverage'] >= 0.0]
        self.assertEqual(len(coverage_values), len(self.coverage))

    def test_coverage_is_sorted(self):
        coverage_values = [k['coverage'] for k in self.coverage]
        self.assertListEqual(sorted(coverage_values, reverse=True),
                             coverage_values)


if __name__ == '__main__':
    unittest.main()
