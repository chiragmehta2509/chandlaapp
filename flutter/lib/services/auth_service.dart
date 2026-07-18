import 'api_service.dart';
import 'package:shared_preferences/shared_preferences.dart;

class AuthService {
  final ApiService _api = ApiService();
  final String _tokenKey = 'auth_token';

  // Register
  Future<Map<String, dynamic>> register({
    required String name,
    required String email,
    required String password,
  }) async {
    try {
      final response = await _api.post('/auth/register', {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': password,
      });

      if (response['success'] == true) {
        final token = response['data']['token'];
        await _saveToken(token);
        _api.setToken(token);
        return {'success': true, 'data': response['data']};
      }
      return {'success': false, 'message': response['message']};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Login
  Future<Map<String, dynamic>> login({
    required String email,
    required String password,
  }) async {
    try {
      final response = await _api.post('/auth/login', {
        'email': email,
        'password': password,
      });

      if (response['success'] == true) {
        final token = response['data']['token'];
        await _saveToken(token);
        _api.setToken(token);
        return {'success': true, 'data': response['data']};
      }
      return {'success': false, 'message': response['message']};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Google Login
  Future<Map<String, dynamic>> googleLogin({
    required String idToken,
    required String email,
    String? name,
    String? avatar,
  }) async {
    try {
      final response = await _api.post('/auth/google/login', {
        'id_token': idToken,
        'email': email,
        if (name != null) 'name': name,
        if (avatar != null) 'avatar': avatar,
      });

      if (response['success'] == true) {
        final token = response['data']['token'];
        await _saveToken(token);
        _api.setToken(token);
        return {'success': true, 'data': response['data']};
      }
      return {'success': false, 'message': response['message']};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Phone OTP Login
  Future<Map<String, dynamic>> sendOTP(String phone) async {
    try {
      final response = await _api.post('/auth/phone/send-otp', {
        'phone': phone,
      });
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  Future<Map<String, dynamic>> verifyOTP({
    required String phone,
    required String otp,
    String? name,
  }) async {
    try {
      final response = await _api.post('/auth/phone/verify-otp', {
        'phone': phone,
        'otp': otp,
        if (name != null) 'name': name,
      });

      if (response['success'] == true) {
        final token = response['data']['token'];
        await _saveToken(token);
        _api.setToken(token);
        return {'success': true, 'data': response['data']};
      }
      return {'success': false, 'message': response['message']};
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Get current user
  Future<Map<String, dynamic>> getCurrentUser() async {
    try {
      final response = await _api.get('/auth/me');
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Logout
  Future<void> logout() async {
    try {
      await _api.post('/auth/logout', {});
    } catch (e) {
      print('Logout error: $e');
    } finally {
      await _clearToken();
      _api.clearToken();
    }
  }

  // Check if logged in
  Future<bool> isLoggedIn() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(_tokenKey);
    if (token != null) {
      _api.setToken(token);
      return true;
    }
    return false;
  }

  // Save token
  Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_tokenKey, token);
  }

  // Clear token
  Future<void> _clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(_tokenKey);
  }
}

