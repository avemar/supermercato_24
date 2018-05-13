import random

class CoverageMockData:
    @staticmethod
    def getLocations():
        areas = []
        area_id = 1
        """
        USE LIST COMPREHENSION!
        """
        for x in range(10):
            areas.append({'id': area_id, 'zip_code': area_id, 'lat': random.uniform(-1, 1) * 90,
                          'lng': random.uniform(-1, 1) * 180})
            area_id += 1
        return areas

    @staticmethod
    def getShoppers():
        shoppers = []
        for x in range(10):
            shoppers.append({'id': 'S{}'.format(random.randint(1, 30)), 'lat': random.uniform(-1, 1) * 90,
                          'lng': random.uniform(-1, 1) * 180, 'enabled': True})
        return shoppers

    @staticmethod
    def haversine(lat1, lng1, lat2, lng2):
        return random.random() * 100

class Coverage:
    MAX_RADIUS = 10.0

    @classmethod
    def test(cls):
        print(CoverageMockData.getShoppers());

if __name__ == '__main__':
    Coverage.test()
