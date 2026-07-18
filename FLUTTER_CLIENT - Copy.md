# 📱 Flutter API Client - Chandla Book

## Complete Flutter Integration Guide

### 1. Create API Service Class

Create `lib/services/api_service.dart`:

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl = 'http://localhost:8000/api/v1';
  String? _token;

  // Set authentication token
  void setToken(String token) {
    _token = token;
  }

  // Clear token on logout
  void clearToken() {
    _token = null;
  }

  // Get headers
  Map<String, String> get _headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (_token != null) 'Authorization': 'Bearer $_token',
  };

  // Handle response
  dynamic _handleResponse(http.Response response) {
    if (response.statusCode >= 200 && response.statusCode < 300) {
      return json.decode(response.body);
    } else {
      throw Exception('Error: ${response.statusCode} - ${response.body}');
    }
  }

  // GET request
  Future<dynamic> get(String endpoint) async {
    final response = await http.get(
      Uri.parse('$baseUrl$endpoint'),
      headers: _headers,
    );
    return _handleResponse(response);
  }

  // POST request
  Future<dynamic> post(String endpoint, Map<String, dynamic> data) async {
    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: _headers,
      body: json.encode(data),
    );
    return _handleResponse(response);
  }

  // PUT request
  Future<dynamic> put(String endpoint, Map<String, dynamic> data) async {
    final response = await http.put(
      Uri.parse('$baseUrl$endpoint'),
      headers: _headers,
      body: json.encode(data),
    );
    return _handleResponse(response);
  }

  // DELETE request
  Future<dynamic> delete(String endpoint) async {
    final response = await http.delete(
      Uri.parse('$baseUrl$endpoint'),
      headers: _headers,
    );
    return _handleResponse(response);
  }
}
```

---

### 2. Create Auth Service

Create `lib/services/auth_service.dart`:

```dart
import 'api_service.dart';
import 'package:shared_preferences/shared_preferences.dart';

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
```

---

### 3. Create Event Service

Create `lib/services/event_service.dart`:

```dart
import 'api_service.dart';

class EventService {
  final ApiService _api = ApiService();

  // Get all events
  Future<Map<String, dynamic>> getEvents({
    int? perPage,
    String? type,
    String? search,
  }) async {
    try {
      String endpoint = '/events?';
      if (perPage != null) endpoint += 'per_page=$perPage&';
      if (type != null) endpoint += 'type=$type&';
      if (search != null) endpoint += 'search=$search&';
      
      final response = await _api.get(endpoint);
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Get upcoming events
  Future<Map<String, dynamic>> getUpcomingEvents() async {
    try {
      final response = await _api.get('/events/upcoming');
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Get event by ID
  Future<Map<String, dynamic>> getEvent(int id) async {
    try {
      final response = await _api.get('/events/$id');
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Create event
  Future<Map<String, dynamic>> createEvent({
    required String title,
    String? description,
    required String eventDate,
    String? eventTime,
    String? venue,
    String? eventType,
  }) async {
    try {
      final response = await _api.post('/events', {
        'title': title,
        if (description != null) 'description': description,
        'event_date': eventDate,
        if (eventTime != null) 'event_time': eventTime,
        if (venue != null) 'venue': venue,
        if (eventType != null) 'event_type': eventType,
      });
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Update event
  Future<Map<String, dynamic>> updateEvent(int id, Map<String, dynamic> data) async {
    try {
      final response = await _api.put('/events/$id', data);
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Delete event
  Future<Map<String, dynamic>> deleteEvent(int id) async {
    try {
      final response = await _api.delete('/events/$id');
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Archive event
  Future<Map<String, dynamic>> archiveEvent(int id) async {
    try {
      final response = await _api.post('/events/$id/archive', {});
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }
}
```

---

### 4. Create Contact Service

Create `lib/services/contact_service.dart`:

```dart
import 'api_service.dart';
import 'dart:io';

class ContactService {
  final ApiService _api = ApiService();

  // Get all contacts
  Future<Map<String, dynamic>> getContacts({int? perPage, String? search}) async {
    try {
      String endpoint = '/contacts?';
      if (perPage != null) endpoint += 'per_page=$perPage&';
      if (search != null) endpoint += 'search=$search&';
      
      final response = await _api.get(endpoint);
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Create contact
  Future<Map<String, dynamic>> createContact({
    required String name,
    String? phone,
    String? email,
    String? address,
    String? relationship,
  }) async {
    try {
      final response = await _api.post('/contacts', {
        'name': name,
        if (phone != null) 'phone': phone,
        if (email != null) 'email': email,
        if (address != null) 'address': address,
        if (relationship != null) 'relationship': relationship,
      });
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Update contact
  Future<Map<String, dynamic>> updateContact(int id, Map<String, dynamic> data) async {
    try {
      final response = await _api.put('/contacts/$id', data);
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }

  // Delete contact
  Future<Map<String, dynamic>> deleteContact(int id) async {
    try {
      final response = await _api.delete('/contacts/$id');
      return response;
    } catch (e) {
      return {'success': false, 'message': e.toString()};
    }
  }
}
```

---

### 5. Usage Example

Create `lib/screens/login_screen.dart`:

```dart
import 'package:flutter/material.dart';
import '../services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _authService = AuthService();
  bool _isLoading = false;

  Future<void> _login() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);

      final result = await _authService.login(
        email: _emailController.text,
        password: _passwordController.text,
      );

      setState(() => _isLoading = false);

      if (result['success'] == true) {
        Navigator.pushReplacementNamed(context, '/home');
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Login failed')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Login')),
      body: Padding(
        padding: EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                controller: _emailController,
                decoration: InputDecoration(labelText: 'Email'),
                validator: (value) => value?.isEmpty ?? true ? 'Required' : null,
              ),
              TextFormField(
                controller: _passwordController,
                decoration: InputDecoration(labelText: 'Password'),
                obscureText: true,
                validator: (value) => value?.isEmpty ?? true ? 'Required' : null,
              ),
              SizedBox(height: 20),
              _isLoading
                  ? CircularProgressIndicator()
                  : ElevatedButton(
                      onPressed: _login,
                      child: Text('Login'),
                    ),
            ],
          ),
        ),
      ),
    );
  }
}
```

---

### 6. Add Dependencies to pubspec.yaml

```yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0
  shared_preferences: ^2.2.2
```

---

### 7. Update API Base URL

For production, update `baseUrl` in `api_service.dart`:

```dart
static const String baseUrl = 'https://api.chandlabook.com/api/v1';
```

---

## Complete Integration Checklist

- [ ] Create API service class
- [ ] Create Auth service
- [ ] Create Event service
- [ ] Create Contact service
- [ ] Create Entry service
- [ ] Create Invitation service
- [ ] Create Payment service
- [ ] Add error handling
- [ ] Add loading states
- [ ] Add token management
- [ ] Test all endpoints
- [ ] Handle offline mode
- [ ] Add retry logic

---

**Flutter Integration Complete! 📱**

